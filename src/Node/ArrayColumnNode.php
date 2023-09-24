<?php
namespace Loader\Utility\Node;

use essentials\addon\NodeEditor\Node;

class ArrayColumnNode extends Node{

    public $in = [
        'column'  => 'String',
        'array' => 'Array'
    ];
    public $out = [
        'array'  => 'Array'
    ];

    public $xmlWriter;

    public function method(array $inputs){
        return ['array' => array_column($inputs['array'],$inputs['column'])];
    }
}

