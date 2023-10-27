<?php
namespace Fortles\NodeEditor\Node;
use Exception;
use Fortles\NodeEditor\DynamicNodeInterface;
use Fortles\NodeEditor\Node;

class InputNode extends Node implements DynamicNodeInterface{
    function __construct(array $data){
        $this->out = $thisenvironment->inputData;
        parent::__construct($data);
    }
    function method(array $inputs){
        return $thisenvironment->inputData;
    }
}