<?php
namespace Fortles\NodeEditor\Node;
use Fortles\NodeEditor\Node;

class ArrayMathNode extends Node{
    public $in = [
        'operation' => [
            'type' => 'select',
            'values' => [
                'sum','min','max','avg'
            ]
        ],
        'array' => 'array',
    ];
    public $out = [
        'result' => 'number'
    ];
    public function method(array $inputs) {
        if(!is_array($inputs['array'])){
            return ['result' => null];
        }
        switch ($inputs['operation']){
            case 0 : $c = array_sum($inputs['array']); break;
            case 1 : $c = min($inputs['array']); break;
            case 2 : $c = max($inputs['array']); break;
            case 3 : $c = array_sum($inputs['array']) / count($inputs['array']); break;
        }
        return ['result' => $c];
    }
}
