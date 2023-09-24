<?php
namespace Loader\Utility\Node;

use Cake\Utility\Hash;
use essentials\addon\NodeEditor\Node;

class GetValueNode extends Node{
    
    public $in = [
        'input' => 'object',
        'path'  => 'string'
    ];
    
    public $out = [
        'output' => 'mixed'
    ];
    
    public function method(array $inputs) {
        return ['output' => empty($inputs['input']) ? null : Hash::get($inputs['input'], $inputs['path'])];
    }
}
