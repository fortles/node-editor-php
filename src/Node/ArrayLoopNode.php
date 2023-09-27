<?php
namespace Fortles\NodeEditor\Node;

use Fortles\NodeEditor\DynamicNodeInterface;
use Fortles\NodeEditor\Node;

class ArrayLoopNode extends Node implements DynamicNodeInterface{
    
    public $in = [
        'array' => 'Array'
    ];
    
    protected $index = -1;
    
    public function init(array $inputs) {
        $array = $inputs['array'][0] ?? null;
        if(!empty($array)){
            $this->setOut(array_combine(array_keys($array), array_keys($array)));
        }
        parent::init($inputs);
    }
    public function method(array $inputs) {
        $this->index++;
        //debug($this->index);
        return $inputs['array'][$this->index] ?? false;
    }
    public function reset() {
        $this->index = -1;
        parent::reset();
    }
}