<?php
namespace Loader\Utility\Node;
use Cake\I18n\FrozenDate;
use essentials\addon\NodeEditor\Node;

class DateFormatNode extends Node{

    public $in = [
        'date' => 'Date',
        'format' => 'String'
    ];

    public $out = [
        'output' => 'String'
    ];

    public function method(array $inputs) {
        if($inputs['date'] instanceof FrozenDate){
            return ['output' => $inputs['date']->i18nFormat($inputs['format']) ];
        }else{
            return ['output' => null];
        }
    }

}