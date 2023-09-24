<?php
namespace Loader\Utility\Node;

use Car\Utility\SkippingXMLWriter;
use essentials\addon\NodeEditor\OutputNode;

class XMLOutputNode extends OutputNode{
    
    public $in = [
        'name' => 'String',
        'xml' => 'XMLWriter'
    ];
    
    private $xmlWriter;
    
    
    public function init(array $inputs) {
        parent::init($inputs);
    }
    
    public function prepare() {
        $this->xmlWriter = new SkippingXMLWriter();
        $this->backPropagate(function($node){
            if(property_exists($node,'xmlWriter')){
                $node->xmlWriter = $this->xmlWriter;
            }else{
                return false;
            }
        });
        parent::prepare();
    }
    
    public function calculate($cycle = null) {
        header('Content-Type: application/xml');
        $this->xmlWriter->openURI('php://output');
        //echo '<table>';
        $this->xmlWriter->startDocument("1.0");
        $this->xmlWriter->startElement($this->inputBuffer['name']);
        parent::calculate($cycle);
        $this->xmlWriter->endElement();
        $this->xmlWriter->endDocument();
        $this->xmlWriter->flush();
        //echo '</table>';
    }
    
    public function method(array $inputs) {
        
    }

    public function setData(array $inputs) {
        return $this->xmlWriter;
    }

}