<?php
namespace Loader\Utility\Node;

use essentials\addon\NodeEditor\Node;
use essentials\addon\NodeEditor\RenderNodeInterface;

class XMLAttributesNode extends Node{
    
    public $in = [
        'xml' => 'xml',
    ];
    public $out = [
        'xml' => 'xml',
    ];
    
    public $xmlWriter;
    
    public function init(array $inputs) {
        unset($this->in['xml']);
        $this->in['xml'] = 'xml';
    }

        public function method(array $inputs){
        return ['xml' => $this];
    }
    
    function calculate($cycle = null){
        if($cycle === $this->cycle){
            return null;
        }
        $this->cycle = $cycle;
        $stopColor = null;
        //Get inputs
        if(isset($this->in)){
            foreach($this->in as $key => $value){
                if(isset($this->connections[$key])){
                    $connected = $this->connections[$key][0];
                    //Recaulculate if onnected color not smaller
                    if($connected->color >= $this->color || $this->inputBuffer === false){
                        $stopColor = $connected->calculate($cycle);
                    }
                    $input = $connected->outputBuffer[$this->connections[$key][1]];
                    if(!$input instanceof Node){
                        if($key == 'xml'){
                            $this->xmlWriter->text($input);
                        }else{
                            $this->xmlWriter->writeAttribute($key, $input);
                        }
                    }
                }else if(isset($this->values[$key])){
                    $this->xmlWriter->writeAttribute($key, $this->values[$key]);
                }
            }
        }
        $this->outputBuffer = $this->method($this->inputBuffer);
        if($this->outputBuffer === false){
            return $this->color;
        }
        return $stopColor;
    }
}

