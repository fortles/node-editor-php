<?php
namespace Loader\Utility\Node;
use essentials\addon\NodeEditor\DynamicNodeInterface;
use essentials\addon\NodeEditor\Node;

class ForCycleNode extends Node implements DynamicNodeInterface {

    public $in = [
        'from' => 'int',
        'to' => 'int',
        'increment' => 'int',
    ];

    protected $i = 0;
    public $out = [
        'value' => 'int'
    ];

    public function init(array $inputs) {

        parent::init($inputs);
        $this->i = $inputs['from'];
    }

    public function method(array $inputs) {
        $i = $this->i;
        $this->i += $inputs['increment'];

        if($i > $inputs['to']){
            return FALSE;
        }
        return [
            'value' => $i
        ];
    }

    public function reset()
    {
        $this->i = $this->inputBuffer['from'];
        parent::reset();
    }
}