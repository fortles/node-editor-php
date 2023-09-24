<?php
namespace Fortles\NodeEditor;

abstract class OutputNode extends Node{
    protected $lastData;
    public function __construct(array $data) {
        parent::__construct($data);
        $this->editor->outputs[$this->name] = $this;
    }
    public function calculate($cycle = null) {
        if($cycle === $this->cycle){
            return null;
        }
        $stopColor = parent::calculate($cycle);
        if($stopColor === null && isset($cycle)){
            $this->lastData = $this->setData($this->inputBuffer);
        }
        return $stopColor;
    }
    abstract function setData(array $inputs);
    public function getData(){
        return $this->lastData;
    }
}