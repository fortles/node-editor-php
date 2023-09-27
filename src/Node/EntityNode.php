<?php
namespace Fortles\NodeEditor\Node;

use Fortles\NodeEditor\Node;

class EntityNode extends Node{
    
    public $in = [
        'entity' => 'Entity'
    ];
    
    public function init(array $inputs) {
        $entity = $inputs['entity'];
        if(!empty($entity)){
            $this->setOut(array_combine($entity->visibleProperties(), $entity->visibleProperties()));
        }
        parent::init($inputs);
    }
    
    public function method(array $inputs) {
        return $inputs['entity'];
    }
}