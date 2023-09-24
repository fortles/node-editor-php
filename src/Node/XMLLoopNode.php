<?php

namespace NodeEditor\Node;

use NodeEditor\Utility\DynamicNodeInterface;

class XMLLoopNode extends XMLNode implements DynamicNodeInterface{
    public function init(array $inputs) {
        parent::init($inputs);
        $keys = array_keys($this->xml->getValues($this->element));
        $this->setOut(array_combine($keys, $keys));
        parent::init($inputs);
    }
    public function method(array $inputs) {
        return $this->xml->getValues($this->element, $this->connectedOut);
    }
}