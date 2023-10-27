<?php
namespace Fortles\NodeEditor\Node;
use Exception;
use Fortles\NodeEditor\DynamicNodeInterface;
use Fortles\NodeEditor\Node;

class InputNode extends Node implements DynamicNodeInterface{
    function __construct(array $data){
        parent::__construct($data);
        $this->out = $this->getEnvironment()->getInputData();
    }
    function method(array $inputs){
        return $this->getEnvironment()->getInputData();
    }
}