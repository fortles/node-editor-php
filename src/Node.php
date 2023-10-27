<?php
namespace Fortles\NodeEditor;

abstract class Node{
    //@var NodeEditorModule Holds the editor wich includes this node.
    private NodeEnvironment $environment;
    //@var array values The values used if the input is not cennected.
    public $values;
    //@var string name The name of the node.
    protected $name;
    //@var mixed userData Custom data that can be stored.
    public $userData;
    //@var mixed color A number represent
    public $color = false;
    public $in = [];
    public $out = [];
    public $connectedOut = [];
    protected $inputBuffer = [];
    protected $outputBuffer = [];
    //@var array Nodes wich this node connected to
    protected $connections;
    public $cycle = 0;
    private $dirtyUserData = false;
    function __construct(array $data) {
        $this->environment = $data['environment'];
        $this->name = isset($data['name']) ? $data['name'] : null;
        $this->userData = isset($data['userdata']) ? $data['userdata'] : null;
        if(!isset($this->environment->nodes[$this->name])){
            $this->environment->nodes[$this->name] = $this;
        }
    }
    function __destruct() {
        if($this->dirtyUserData){
            $this->environment->nodes[$this->name] = $this;
            $this->environment->saveUserData();
        }
    }

    abstract function method(array $inputs);
    
    function calculate($cycle = null){
        if($cycle === $this->cycle){
            return null;
        }
//        echo "
//<tr>
//    <td>$this->name</td>
//    <td>$this->cycle</td>
//    <td>$cycle</td>
//</tr>";
        $this->cycle = $cycle;
        $stopColor = null;
        //Get inputs
        if(isset($this->in)){
            foreach($this->in as $key => $value){
                if(isset($this->connections[$key])){
                    $connected = $this->connections[$key][0];
                    //Recaulculate if onnected color not smaller
                    if($connected->color >= $this->color || $this->inputBuffer === false){
                        $stopColor = $connected->calculate($cycle);
                        if(isset($stopColor)){
                            return $stopColor;
                        }
                    }
                    $this->inputBuffer[$key] = $connected->outputBuffer[$this->connections[$key][1]] ?? null;
                }else{
                    $this->inputBuffer[$key] = isset($this->values[$key]) ? $this->values[$key] : null;
                }
            }
        }
        $this->outputBuffer = $this->method($this->inputBuffer);
        if($this->outputBuffer === false){
            return $this->color;
        }
        return $stopColor;
    }
    function init(array $inputs){}
    function prepare(){
        if($this->color !== false){
            return;
        }
        //Additional inputs
        if(isset($this->userData['in'])){
            $this->in += $this->userData['in'];
        }
        if(isset($this->userData['out'])){
            $this->out += $this->userData['out'];
        }
        $this->color = $this instanceof  DynamicNodeInterface ? 1 : 0;
        if(isset($this->in)){
            foreach ($this->in as  $key => $type){
                if(isset($this->environment->connections[$this->name],$this->environment->connections[$this->name][$key])){
                    $connection = $this->environment->connections[$this->name][$key];
                    $connected = $this->environment->nodes[$connection[0]];
                    $connected->connectedOut[$connection[1]] = true;
                    $connected->prepare();
                    $this->connections[$key] = [$connected, $connection[1]];
                    $this->color = $connected->color;
                    if($this instanceof DynamicNodeInterface){
                        $this->color++;
                    }
                    if($connected instanceof CollectorNode){
                        $this->color--;
                    }
                    //echo $this->name . "\n";
                    //echo $this->color;
                    $this->inputBuffer[$key] = $connected->outputBuffer[$this->connections[$key][1]];
                }else{
                    $this->inputBuffer[$key] = isset($this->values[$key]) ? $this->values[$key] : null;
                }
            }
        }
        $this->init($this->inputBuffer);
        $this->outputBuffer = $this->method($this->inputBuffer);
        $this->environment->colors[$this->color] []= $this;
        return $this->dirtyUserData;
    }
    function setUserData($key, $value){
        if(!isset($this->userData[$key]) || $this->userData[$key] != $value){
            $this->userData[$key] = $value;
            $this->dirtyUserData = true;
        }
    }
    function getUserData($key = null){
        if(empty($key)){
            return $this->userData;
        }else if(isset($this->userData[$key])){
            return is_array($this->userData[$key]) ? $this->userData[$key] : htmlspecialchars_decode($this->userData[$key]);
        }else{
            return null;
        }
    }
    function setIn($in){
        $this->setUserData('in', $in);
    }
    function setOut($out){
        $this->setUserData('out', $out);
    }
    function toView(){
        return ['data'=>null];
    }
    function view($data) {
        try{
            $this->environment->init();
        }catch(Exception $ex){
            
        }
        echo "<h1>Edit $this->name</h1><form class='e-form-underline'";
        $this->userViewInit();
        Html::form('edit');
        echo '/>';
        $this->userView();
        echo '<div class="e-action-bar" >';
        Html::button('<i class="fa fa-floppy-o e-button-icon"></i>');
        echo '</div>';
        echo '</form>';
        $this->userViewDebug();
    }
    
    protected function userViewDebug(){
        echo "<h2>Debug</h2>";
        echo "<h3>Inputs</h3>";
        Html::dl($this->inputBuffer, null, function($data){return "<pre>$data</pre>";});
        echo "<h3>Outpus</h3>";
        Html::dl($this->outputBuffer, null, function($data){return "<pre>$data</pre>";});
    }
    
    function userViewInit(){
        $this->edit['label']['default'] = $this->getUserData('label');
    }
    function userView(){
        Html::input('label','placeholder="label"');
    }

    public $edit = [
        'label' => [
            'filter' => FILTER_SANITIZE_STRING
        ]
    ];
    function edit($label){
        if($label){
            $this->setUserData('label', $label);
        }
        return [
            self::FEEDBACK_DONE => 'Saved!',
            self::GO => '../'
        ];
    }
    function getName(){
        return $this->name;
    }
    protected function getConnected($input){
        if($this->connections){
            return $this->connections[$input][0] ?? null;
        }else{
             $connection = $this->environment->connections[$this->name][$input] ?? null;
             if(!empty($connection)){
                return $this->environment->nodes[$connection[0]] ?? null;
             }
        }
    }
    
    public function backPropagate($callable){
        $connections = $this->environment->connections[$this->name] ?? null;
        if(!empty($connections)){
            foreach($connections as $connection){
                $connectedNode = $this->environment->nodes[$connection[0]];
                if($callable($connectedNode) !== false){
                    $connectedNode->backPropagate($callable);
                }
            }
        }
    }

    public function reset(){
        $this->cycle = -1;
    }

    /**
     * Returns a config value form the environment or a default if not provided.
     * The config can be set on the environment, in the constructor or with the setConfig of the constructor.
     * @param string $key Name of the config
     * @param mixed $default The default value if config has no value, when not provided its null.
     */
    public function getConfig(string $key, $default = null){
        return $this->environment->getConfig($key, $default);
    }

    public function getEnvironment() : NodeEnvironment {
        return $this->environment;
    }
}

class ArrayNode extends Node{
    public $out = ['Array' => 'array'];
    
    public function method(array $inputs) {
        return ['Array' => $inputs];
    }
    public function init(array $inputs) {
        $this->setIn([0,0]);
    }
}
interface DynamicNodeInterface{

}
