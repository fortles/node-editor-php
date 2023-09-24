<?php

namespace Loader\Utility\Node;

use essentials\addon\NodeEditor\DynamicNodeInterface;

class XMLToArrayNode extends XMLNode implements DynamicNodeInterface
{

    public $out = [
        'output' => 'Array',
    ];

    protected $ret = [];

    public function init(array $inputs)
    {
        parent::init($inputs);
    }

    public function method(array $inputs)
    {

        if(!empty($this->xml->getValues($this->element))) {
            if($this->ret === []) {
                while ($value = $this->xml->getValues($this->element)) {
                    if(!empty($value)) {
                        $this->ret[] = $value;
                    }
                }
            }

        }
        return ['output' => $this->ret];

    }
}