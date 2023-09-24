<?php
namespace Loader\Utility\Node;

use App\Model\Domain\PhonePattern;
use essentials\addon\NodeEditor\Node;

class PhoneInfoNode extends Node{

    public $in = [
        'phone' => 'String'
    ];

    public $out = [
        'country'   => 'String',
        'area'      => 'number',
        'number'    => 'number',
        'formatted' => 'String',
    ];

    public function method(array $inputs) {
        return PhonePattern::info($inputs['phone']) ?: [
            'country'   => null,
            'area'      => null,
            'number'    => null,
            'formatted' => null,
        ];
    }
}