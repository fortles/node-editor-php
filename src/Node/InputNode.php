<?php
namespace Fortles\NodeEditor\Node;
use Exception;
use Fortles\NodeEditor\DynamicNodeInterface;
use Fortles\NodeEditor\Node;

class InputNode extends Node implements DynamicNodeInterface{
    function __construct(array $data){
        $this->out = $this->editor->inputData;
        parent::__construct($data);
    }
    function method(array $inputs){
        return $this->editor->inputData;
    }
}