<?php
namespace Fortles\NodeEditor\Node;
use Fortles\NodeEditor\Node;
class MathNode extends Node{
    public $in = [
        'Operation' => [
            'type' => 'select',
            'values' => [
                'A + B','A - B','A × B','A ÷ B', 'A % B'
            ]
        ],
        'A' => 'number',
        'B' => 'number',
    ];
    public $out = [
        'Result' => 'number'
    ];
    public function method(array $inputs) {
        $a = $this->num($inputs['A']);
        $b = $this->num($inputs['B']);
        switch ($inputs['Operation']){
            case 0 : $c = $a + $b; break;
            case 1 : $c = $a - $b; break;
            case 2 : $c = $a * $b; break;
            case 3 : $c = $a / $b; break;
            case 4 : $c = $a % $b; break;
        }
        return ['Result' => $c];
    }
    public function num($input) {
        return floatval(str_replace([',',' '], ['.',''], $input));
    }
}
