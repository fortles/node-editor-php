<?php
namespace NodeEditor;

abstract class CollectorNode extends Node{
    protected $dynamicNode; //legközelebbi node, amit loopolunk
    protected $overrun = 10000;
    public function init(array $inputs) {
        $this->backPropagate(function($node){
            if($node->color == $this->color && $node instanceof DynamicNodeInterface){
                $this->dynamicNode = $node;
                return false;
            }
        });
        parent::init($inputs);
    }
    public function calculate($cycle = null) {
        $stopColor = null;
        $this->reset();
        while($this->dynamicNode->calculate($cycle) === null && $this->overrun--){
            //echo "$this->name\tcycle:$cycle\told:$cycle\n";
            $stopColor = parent::calculate($cycle);
            $cycle++;
        }
        $this->dynamicNode->reset();
        return $stopColor;
    }
}