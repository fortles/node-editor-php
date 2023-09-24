<?php
namespace Loader\Utility\Node;
use essentials\addon\NodeEditor\OutputNode;

class CreateOutputNode extends OutputNode{
    
    public $in = [
        'synced' => [
            'type' => 'select',
            'values' => [
                0 => 'No',
                1 => 'Yes'
            ]
        ],
        'success' => 'bool',
        'externalId' => 'string',
        'error' => 'string'
    ];
    
    public function method(array $inputs) { }

    public function setData(array $inputs) {
        return $inputs;
    }
    
    public function isSynced(){
        return !!$this->outputBuffer['synced'];
    }

}