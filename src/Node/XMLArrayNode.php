<?php
namespace Loader\Utility\Node;

use essentials\addon\NodeEditor\Node;

class XMLArrayNode extends Node{
    
    public $in = [
        'name'  => 'String',
        'array' => 'Array'
    ];
    public $out = [
        'xml'  => 'xml'
    ];
    
    public $xmlWriter;
   
    public function method(array $inputs){
        foreach($inputs['array'] as $value){
            if(!empty($this->xmlWriter)) {
                @$this->xmlWriter->startElement($inputs['name']);
                @$this->xmlWriter->text($value);
                @$this->xmlWriter->endElement();
            }
        }
        
        return ['xml' => $this];
    }
}

