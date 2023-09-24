<?php
namespace Loader\Utility\Node;

use essentials\addon\NodeEditor\Node;
use essentials\addon\NodeEditor\RenderNodeInterface;

class XMLNodesNode extends Node{
    
    public $out = [
        'xml' => 'xml',
    ];
    
    public $xmlWriter;
   
    public function method(array $inputs){
        return ['xml' => $this];
    }

    function calculate($cycle = null){
//        echo "
//<tr>
//    <td>$this->name</td>
//    <td>$this->cycle</td>
//    <td>$cycle</td>
//</tr>";
        if($cycle === $this->cycle){
            return null;
        }
        $this->cycle = $cycle;
        $stopColor = null;
        //Get inputs
        if(isset($this->in)){
            foreach($this->in as $key => $value){
                $this->xmlWriter->startElement($key);

                if(isset($this->connections[$key])){

                    $connected = $this->connections[$key][0];
                    //Recaulculate if connected color not smaller
                    if($connected->color >= $this->color || !empty($connected->xmlWriter)){
                        $stopColor = $connected->calculate($cycle);
                    }
                    $input = $connected->outputBuffer[$this->connections[$key][1]];
                    if(!$input instanceof Node){
                        if(!empty($input) || $input === 0){
                            $this->xmlWriter->text($input);
                        }
                    }
                }else if(isset($this->values[$key])){
                    $this->xmlWriter->text($this->values[$key]);
                }
                $this->xmlWriter->endElement();
            }
        }
        $this->outputBuffer = $this->method($this->inputBuffer);
        if($this->outputBuffer === false){
            return $this->color;
        }
        return $stopColor;
    }
}

