<?php
namespace Loader\Utility\Node;
use essentials\addon\NodeEditor\Node;

class OnEmptyNode extends Node{
    
    public $in = [
        'input' => 'mixed',
        'default' => 'mixed'
    ];
    
    public $out = [
        'output' => 'Array'
    ];
    
    public function method(array $inputs) {
        if(empty($inputs['input'])){
            return ['output' => $inputs['default']];
        }else{
            return ['output' => $inputs['input']];
        }
    }

}