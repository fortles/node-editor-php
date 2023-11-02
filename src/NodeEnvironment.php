<?php
namespace Fortles\NodeEditor;
use Fortles\NodeEditor\Exception\NodeEnvironmentException;

/**
 * Description of NodeEnvironment
 *
 * @author Ivan
 */
class NodeEnvironment {
    /** @var OutputNode[] */
    public $outputs = [];
    public $connections;
    public $nodes;
    public $types = [];
    private $cycle = 0;

    protected $isInited = false;
    public $load = [];

    protected $getData;
    protected $setData;

    protected $config = [];

    protected $inputData = [];

    /**
     * Creates a new node environment
     * @param \Closure $getDataCallback This function will be called, when the environment need to read data to from persistent storage. The loading logic must be defined here.
     * @param \Closure $setDataCallback  This function will be called, when the environment need to save data to the persistent storage. The saving logic must be defined here.
     * @param array $types An associative list of name and type classes like so: `['Math' => MathNode::class]`
     * @param array $config Values bound to the node editor. configs can be read on the nodes as well.
     * @param array $inputData Initial value for input data. This is good to give a shape of the data for the input node.
     */
    public function __construct(\Closure $getDataCallback, \Closure $setDataCallback, array $types, array $config = [], array $inputData = []) {
        $this->getData = $getDataCallback;
        $this->setData = $setDataCallback;
        $this->config = $config;
        $this->inputData = $inputData;
        foreach($types as $key => $value){
            if(is_int($key)){
                $this->types[substr($value, strrpos($value, '\\') + 1, -strlen('Node'))] = $value;
            }else{
                $this->types[$key] = $value;
            }
        }
    }
       
    function load(){
        $json = ($this->getData)();
        $data = [
            'types' => []
        ];
        if($json){
            $data = json_decode($json,true);
            $type = [];
            foreach ($data['nodes'] as $name => &$nodeData){
                if(!isset($type[$nodeData['type']])){
                    if(empty($this->types[$nodeData['type']])){
                        throw new \Exception('There is no type definition for "'.$nodeData['type'].'"');
                    }
                    $node = new $this->types[$nodeData['type']]([
                        'environment' => $this,
                        'name' => $name,
                        'userdata' => isset($nodeData['userdata']) ? $nodeData['userdata'] : null
                    ]);
                    $nodeData['userdata'] = $node->getUserData();
                }
                if(!isset($type[$nodeData['type']])){
                    $type[$nodeData['type']] = [
                        'in' => $node->in,
                        'out'=> $node->out
                    ];
                }
            }
            $data['types'] = $type;
        }
        $data['add'] = array_keys($this->types);
        return $data;
    }

    function getType($name){
        $types = $this->types;
        if(isset($types[$name])){
            $n = new $types[$name]([
                'environment' => $this,
                'name' => ''
            ]);
            return [
                'in'  => $n->in,
                'out' => $n->out
            ];
        }
    }
    
    protected function build(){
        //Load data
        $data = json_decode(($this->getData)(),true) ?? [];
        //Create nodes
        foreach ($data['nodes'] as $name => $node){
            new $this->types[$node['type']]([
                'environment' => $this,
                'name' => $name,
                'userdata' => isset($node['userdata']) ? $node['userdata'] : null
            ]);
            if(isset($node['values'])){
                $this->nodes[$name]->values = $node['values'];
            }
        }
        //Connect nodes
        if(isset($data['connections'])){
            $this->connections = $data['connections'];
        }
    }
    public function getValue($name, $out){
        return $this->nodes[$name]->get($out);
    }
    public function saveUserData(){
        $data = json_decode(($this->getData)(),true);
        foreach ($this->nodes as $name => $node){
            if(isset($node->userData)){
                $data['nodes'][$name]['userdata'] = $node->userData;
            }
        }
        $json = json_encode($data);
        if($json === false){
            throw new \Exception("Cant encode data");
        }
        ($this->setData)($json);
    }
    
    public function init(){
        $this->build();
        $dirty = false;
        foreach ($this->nodes as $name => $node){
            if($node->color === false){
                if($node->prepare()){
                    $dirty = true;
                }
            }
            $node->reset();
        }
        
        if($dirty){
            $this->saveUserData();
        }
        $this->isInited = true;
    }
    public function reset(){
        if(isset($this->nodes)){
            foreach ($this->nodes as $node){
                $node->reset();
            }
        }
        $this->cycle = 0;
    }

    public function test(array $data = [], string $nodeName = null){
        $this->inputData = $data;
        $this->init();
        if(isset($nodeName)){
            if(isset($this->outputs[$nodeName])){
                $node = $this->outputs[$nodeName];
                $node->calculate();
            }else{
                throw new \Exception("Node name '$nodeName' not found");
            }
        }else{
            foreach ($this->outputs as $node){
                $node->calculate();
            }
        }
    }
    
    /**
     * Calculates the next step
     * @param mixed $data Input data for the node
     * @param string $nodeName If given the next step will calculated for that node only.
     * @return mixed Return the result of the selected node, or true if no `$nodeName` given. False if there is no new step available.
     */
    public function next($data = null, string $nodeName = null){
        if($data != null){
            $this->inputData = $data;
        }
        if(!$this->isInited()){
            $this->init();
        }
        if(isset($nodeName)){
            $node = $this->nodes[$nodeName];
            if($node->calculate($this->cycle++) === null){
                return $node->getData();
            }else{
                return false;
            }
        }else{
            $busy = false;
            foreach ($this->outputs as $node){
                if($node->color === 0 && $this->cycle === 0){
                    $node->calculate($this->cycle);
                }else if($node->color > 0){
                    if($node->calculate($this->cycle) !== false){
                        $busy = true;
                    }
                }
            }
            return $busy;
        }
    }
    public function run(array $data = []){
        $this->inputData = $data;
        if($this->cycle == 0){
            $this->init();
        }else{
            $this->reset();
        }
        $cycle = 0;
        do{
            $busy = false;
            foreach ($this->outputs as $node){
                if($node->color === 0 && $cycle === 0){
                    //var_dump($node->getName(),$node->color);
                    $node->calculate($cycle);
                }else if($node->color > 0){
                    //var_dump($node->getName(),$node->color);
                    if($node->calculate($cycle) !== false){
                        $busy = true;
                    }
                    //var_dump($asd);
                }
            }
            $cycle++;
            $this->cycle++;
        }while($busy);
    }
    
    public function getNode($name): ?Node{
        if(empty($this->nodes)){
            $this->build();
        }
        return $this->nodes[$name] ?? null;
    }
    
    public function isInited(){
        return $this->isInited;
    }

    /**
     * Returns a config value, or a default if not provided
     * @param string $key Name of the config
     * @param mixed $default The default value if config has no value, when not provided its null.
     */
    public function getConfig(string $key, $default = null){
        return $this->config['key'] ?? $default;
    }

    /**
     * Sets a configuration with a given key
     * @param string $key key of the configuration
     */
    public function setConfig(string $key, $value){
        return $this->config[$key] = $value;
    }

    /** 
     * Returns the data set for the current cycle.
     * */
    public function getInputData(){
        return $this->inputData;
    }

    /**
     * Returns the ouput data for a given output node
     */
    public function getOutputData(string $nodeName): array{
        return $this->getOuputNode($nodeName)->getData();
    }

    /**
     * Reutrns an iterable for all of the nodes
     * @return \Generator<array<array>>
     */
    public function getAllOuputData(string $type = null): \Generator{
        foreach($this->outputs as $name => $node){
            if(!isset($type) || $node instanceof $type){
                yield $name => $node->getData();
            }
        }
    }

    /**
     *  Returns an ouput node.
     * @param string $nodeName Name of the requested node
     * @throws NodeEnvironmentException If the node does not exists
     */
    public function getOuputNode(string $nodeName): OutputNode{
        if(!isset($this->outputs[$nodeName])){
            throw new NodeEnvironmentException('Node "' . $nodeName .'" does not exists');
        }
        return $this->outputs[$nodeName];
    }

    /**
     * Returns all ouput node.
     * @return OutputNode[]
     */
    public function getAllOuputNode(): array{
        return $this->outputs;
    }
}
