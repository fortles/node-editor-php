<?php
namespace Fortles\NodeEditor;

/**
 * Description of NodeEnvironment
 *
 * @author Ivan
 */
class NodeEnvironment {
    public $outputs = [];
    public $connections;
    public $nodes;
    public $types = [];
    private $cycle = 0;

    protected $isInited = false;
    public $load = [];

    protected $getData;
    protected $setData;

    public function __construct(\Closure $getDataCallback, \Closure $setDataCallback, array $types) {
        $this->getData = $getDataCallback;
        $this->setData = $setDataCallback;
        foreach($types as $key => $value){
            if(is_int($key)){
                $this->types[$value] = '\Fortles\NodeEditor\Node\\'.$value;
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
                        'editor' => $this,
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
                'editor' => $this,
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
                'editor' => $this,
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

    public function test($node_name = null){
        $this->init();
        if(isset($node_name)){
            if(isset($this->outputs[$node_name])){
                $node = $this->outputs[$node_name];
                $node->calculate();
            }else{
                throw new \Exception("Node name '$node_name' not found");
            }
        }else{
            foreach ($this->outputs as $node){
                $node->calculate();
            }
        }
    }
    
    public function next($node_name){
        if(isset($node_name)){
            $node = $this->nodes[$node_name];
            if($node->calculate($this->cycle++) === null){
                return $node->getData();
            }else{
                return false;
            }
        }
    }
    public function run(){
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
}
