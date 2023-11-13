<?php
namespace Fortles\NodeEditor\Node;
use Fortles\NodeEditor\Node;

class DateInfoNode extends Node{
    public $in = [
        'date' => 'date',
    ];
    public $out = [
        'year' => 'integer',
        'month' => 'integer',
        'day' => 'integer',
        'hour' => 'integer',
        'minuntes' => 'integer',
        'seconds' => 'integer',
        'season' => 'string'
    ];
    public function method(array $inputs)
    {
        $date = new \DateTime($inputs['date']);

        $year = (int) $date->format('Y');
        $month = (int) $date->format('m');
        $day = (int) $date->format('d');
        $hour = (int) $date->format('H');
        $minutes = (int) $date->format('i');
        $seconds = (int) $date->format('s');

        $season = '';
        if ($month >= 3 && $month <= 5) {
            $season = 'Spring';
        } elseif ($month >= 6 && $month <= 8) {
            $season = 'Summer';
        } elseif ($month >= 9 && $month <= 11) {
            $season = 'Autumn';
        } else {
            $season = 'Winter';
        }

        return [
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'hour' => $hour,
            'minutes' => $minutes,
            'seconds' => $seconds,
            'season' => $season
        ];
    }
}
