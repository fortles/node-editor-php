<?php
namespace Loader\Utility\Node;
use essentials\addon\NodeEditor\OutputNode;

class JSONOutputNode extends OutputNode{
    
    public $in = [
        'json' => 'object'
    ];
    
    public function method(array $inputs) {
        
    }

    public function setData(array $inputs) {
        header('Content-Type: application/json');
        echo json_encode($inputs['json'],JSON_FORCE_OBJECT);
    }

}