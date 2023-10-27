<?php
namespace Fortles\NodeEditor\Node;
use Fortles\NodeEditor\Node;

class RangeNode extends Node{
    public $in = [
        'start1' => 'mixed',
        'end1' => 'mixed',
        'operation' => [
            'type' => 'select',
            'values' => [
                'between',
                'outside',
                'overlaps'
            ]
        ],
        'start2' => 'mixed',
        'end2' => 'mixed',
    ];
    public $out = [
        'result' => 'boolean'
    ];
    public function method(array $inputs) {
        switch ($inputs['operation']){
            case 0 : $c = $inputs['start1'] <= $inputs['start2'] && $inputs['end1']   <= $inputs['end2']; break;
            case 1 : $c = $inputs['start1'] >  $inputs['end2']   && $inputs['start2'] >  $inputs['end1']; break;
            case 2 : $c = $inputs['start1'] <= $inputs['end2']   && $inputs['start2'] <= $inputs['end1']; break;
        }
        return ['result' => $c];
    }
}
