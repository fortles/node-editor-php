<?php
namespace Loader\Utility\Node;
use essentials\addon\NodeEditor\OutputNode;
use \Cake\ORM\TableRegistry;

class DeleteOutputNode extends OutputNode{
    
    public $in = [
        'success' => 'bool',
        'error' => 'string'
    ];
    
    public function method(array $inputs) {
        
    }

    public function setData(array $inputs) {
        return $inputs;
    }

}