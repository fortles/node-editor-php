<?php


namespace Fortles\NodeEditor\Node;
use Fortles\NodeEditor\Node;
class LogicNode extends Node{
    public $in = [
        'Operation' => [
            'type' => 'select',
            'values' => [
                'A = B','A < B','A &le; B','A > B','A &ge; B',
            ]
        ],
        'A' => 0,
        'B' => 0,
        'True' => 0,
        'False' => 0
    ];
    public $out = [
        'Result' => 0
    ];
    public function method(array $inputs) {
        $a = trim($inputs['A']);
        $b = trim($inputs['B']);
        switch ($inputs['Operation']){
            case 0 : $c = $a == $b; break;
            case 1 : $c = $a <  $b; break;
            case 2 : $c = $a <= $b; break;
            case 3 : $c = $a >  $b; break;
            case 4 : $c = $a >= $b; break;
        }
        if(!isset($inputs['True']) && !isset($inputs['False'])){
            return ['Result' => $c];
        }else{
            return ['Result' => $c ? $inputs['True'] : $inputs['False']];
        }
    }
}

