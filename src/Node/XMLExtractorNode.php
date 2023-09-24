<?php

namespace Loader\Utility\Node;


use essentials\addon\NodeEditor\Node;

class XMLExtractorNode extends Node{

    public $in = [
        'xml' => 'xml'
    ];

    public function init(array $inputs) {
        parent::init($inputs);
        $keys = $this->method($inputs);
        $this->setOut(array_combine($keys, $keys));

    }
    public function method(array $inputs) {
        $xml = new \SimpleXMLElement($inputs['xml']);
        return (array) $xml;
    }
}