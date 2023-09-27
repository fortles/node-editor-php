<?php
namespace Fortles\NodeEditor\Node;

use Fortles\NodeEditor\Node;

class ExtractArrayNode extends Node{
    
    public $in = [
        'input' => 'array'
    ];
    
    public function init(array $inputs) {
        $input = $inputs['input'];
        if(!empty($input)){
            $this->setOut(array_combine($input, $input));
        }
        parent::init($inputs);
    }
    
    public function method(array $inputs) {
        return $inputs['input'];
    }
}