<?php
namespace Loader\Utility\Node;
use essentials\addon\NodeEditor\Node;

class ObjectNode extends Node{
    
    public $out = [
        'object' => 'object'
    ];
    
    public function method(array $inputs) {
        return ['object' => $inputs];
    }

}