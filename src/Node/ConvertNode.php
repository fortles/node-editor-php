<?php
namespace Loader\Utility\Node;
use essentials\addon\NodeEditor\Node;

class ConvertNode extends Node{
    
    public $in = [
        'input' => 'String'
    ];
    public $out = [
        'result' => 'String'
    ];
    
    public $map = []; 
    public $default = null; 
    
    public function init(array $inputs) {
        $this->map = $this->getUserData('map');
        $this->default = $this->getUserData('default');
        parent::init($inputs);
    }
    
    public function method(array $inputs) {
        return ['result' => $this->map[$inputs['input']] ?? $this->default];
    }

}