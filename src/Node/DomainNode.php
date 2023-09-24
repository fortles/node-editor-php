<?php
namespace Loader\Utility\Node;

use Cake\Routing\Router;
use essentials\addon\NodeEditor\Node;

class DomainNode extends Node{
    
    public $in = [
        'path'  => 'String'
    ];
    
    public $out = [
        'url'  => 'String'
    ];
    
    public $xmlWriter;
    
    private $domain;
    
    public function init(array $inputs) {
        $this->domain = substr(Router::url('/', TRUE), 0, -1);
    }
   
    public function method(array $inputs){
        return ['url' => $this->domain.$inputs['path']];
    }
}

