<?php
namespace Loader\Utility\Node;

use essentials\addon\NodeEditor\DynamicNodeInterface;
use essentials\addon\NodeEditor\Node;
use essentials\addon\NodeEditor\RenderNodeInterface;

class EntityLoopNode extends Node implements DynamicNodeInterface{
    
    public $in = [
        'enities' => 'Array'
    ];
    
    protected $index = -1;
    
    public function init(array $inputs) {
        $entity = $inputs['enities'][0] ?? null;
        if(!empty($entity)){
            $this->setOut(array_combine($entity->visibleProperties(), $entity->visibleProperties()));
        }
        parent::init($inputs);
    }
    public function method(array $inputs) {
        $this->index++;
        //debug($this->index);
        return $inputs['enities'][$this->index] ?? false;
    }
    public function reset() {
        $this->index = -1;
        parent::reset();
    }
}