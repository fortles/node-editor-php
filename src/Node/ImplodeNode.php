<?php

namespace Loader\Utility\Node;
use essentials\addon\NodeEditor\Node;

class ImplodeNode extends Node{
    public $in = [
        'glue'   => 'string',
        'array'   => 'array'
    ];
    public $out = [
        'string' => 'string'
    ];
    public function method(array $inputs){
        return ['string' => implode($inputs['glue'], $inputs['array'])];
    }
}
