<?php
namespace Loader\Utility\Node;

use essentials\addon\NodeEditor\OutputNode;

class RequiredFieldsOutputNode extends OutputNode{

    public function method(array $inputs) {
    }

    public function setData(array $inputs) {
        $result = [];
        foreach ($this->connections as $connection){
            $connected = $connection[0];
            /**
             * @var Cake Description
             */
            $entity = $connected->inputBuffer['entity'] ?? null;
            if(isset($entity)){
                $result[$entity->getName()][] = $connection[1];
            }
        }
        return $result;
    }

}
