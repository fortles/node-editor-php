<?php
namespace Loader\Utility\Node;

use essentials\addon\NodeEditor\CollectorNode;
use essentials\addon\NodeEditor\RenderNodeInterface;

class XMLCollectorNode extends CollectorNode{
    
    public $in = [
        'nodes' => 'xml'
    ];
    
    public $out = [
        'xml' => 'xml',
    ];
    
    public $xmlWriter;
    
    public function method(array $inputs) {
        return ['xml' => $this];
    }
}