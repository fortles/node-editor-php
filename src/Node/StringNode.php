<?php
namespace NodeEditor\Node;

use NodeEditor\Utility\Node;

class StringNode extends Node{
    public $in = [
        'input' => 'string'
    ];
    public $out = [
        'output' => 'string'
    ];
    public function method(array $inputs) {
        return ['output' => $inputs['input']];
    }
}

