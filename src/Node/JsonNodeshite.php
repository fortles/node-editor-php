<?php
namespace Fortles\NodeEditor\Node;

use Fortles\NodeEditor\Node;

class JsonNode extends Node
{

    public $in = [
        'direction' => [
            'type'   => 'select',
            'values' => [
                0 => 'Encode',
                1 => 'Decode',
            ]
        ],
        'input'     => 'mixed'
    ];

    public $out = [
        'output' => 'mixed'
    ];

    public function method(array $inputs)
    {
        $inputs['input'] = @iconv( 'UTF-16', 'UTF-8', $inputs['input']);
        switch ($inputs['direction']) {
            case 0:
                return ['output' => is_array($inputs['input']) ?  json_encode($inputs['input'], JSON_FORCE_OBJECT) : json_encode($inputs['input'])];
                break;
            case 1:
                return ['output' => json_decode($inputs['input'], TRUE)];
                break;
            default:
                return ['output' => $inputs['direction'] ? json_decode($inputs['input'], TRUE) : json_encode($inputs['input'])];
        }
    }
}
