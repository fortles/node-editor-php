<?php
namespace Loader\Utility\Node;
use essentials\addon\NodeEditor\Node;

class ArrayNode extends Node{

    public $in = [
        'mixed',
        'mixed'
    ];

    public $out = [
        'array' => 'Array',
    ];

    public function method(array $inputs) {
        return ['array' => $inputs];
    }
}