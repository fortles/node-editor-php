<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Loader\Utility\Node;

use essentials\addon\NodeEditor\Node;

/**
 * Description of MapBuilderConvertNode
 *
 * @author barta
 */
class MapBuilderConvertNode extends Node
{
    public $in = [
        'input' => 'mixed',
        'map'   => 'mixed',
        'key'   => 'mixed',
        'value' => 'mixed',
    ];

    public $out = [
        'output' => 'mixed',
    ];

    protected $map = [];

    // public function calculate($cycle = null) {
    //     // $stopColor = null;
    //     // $connected = $this->getConnected('input');
    //     // $stopColor = $connected->calculate($this->cycle);
    //     // $value = $this->filter($connected->outputBuffer[$this->connections['input'][1]]);
    //     // if(!key_exists($value, $this->map)){
    //     //     $stopColor = parent::calculate($cycle);
    //     //     $value = $this->filter($connected->outputBuffer[$this->connections['input'][1]]);
    //     // }
    //     // $this->outputBuffer['output'] = $this->map[$value] ?? null;
    //     // return $stopColor;
    // }

    public function method(array $inputs)
    {

        if(empty($inputs['map'])) {
            return ['output' => NULL];
        }


        if(empty($this->map)) {
            foreach($inputs['map'] as $item) {
                $this->map[$item[$inputs['key']]] = $item[$inputs['value']];
            }
        }
        return ['output' => $this->map[$inputs['input']] ?? NULL];
    }

    protected function filter($string)
    {
        return strtolower(trim($string));
    }

}
