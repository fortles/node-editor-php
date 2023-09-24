<?php

namespace Loader\Utility\Node;
use essentials\addon\NodeEditor\Node;

class PrintfNode extends Node{
    public $in = [
        'Format' => 'string',
        'Args'   => 'array'
    ];
    public $out = [
        'Result' => 'string'
    ];
    public function method(array $inputs){
        return ['Result' => is_array($inputs['Args']) ? vsprintf($inputs['Format'],$inputs['Args']): sprintf($inputs['Format'],$inputs['Args'])];
    }
}
