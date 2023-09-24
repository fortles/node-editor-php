<?php
namespace Loader\Utility\Node;
use essentials\addon\NodeEditor\Node;

class CSVNode extends Node{
    
    public $in = [
        'input' => 'Array'
    ];
    public $out = [
        'result' => 'String'
    ];
    
    public function method(array $inputs) {
        return ['result' => implode(';', $inputs['input'] ?? [])];
    }

}