<?php
namespace Fortles\NodeEditor\Node;
use Fortles\NodeEditor\Node;

class DateInputNode extends Node{
    public $in = [
        'year' => 'integer',
        'month' => 'integer',
        'day' => 'integer',
        'hour' => 'integer',
        'minute' => 'integer',
        'second' => 'integer',
    ];
    public $out = [
        'date' => 'date',
    ];
    public function method(array $inputs)
    {
        $date = \DateTime();
        $date->setDate($inputs['year'], $inputs['month'], $inputs['day']);
        $date->setTime($inputs['hour'] ?? 0, $inputs['minute'] ?? 0, $inputs['second'] ?? 0);
        return ['date' => $date];
    }
}