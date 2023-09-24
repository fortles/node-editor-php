<?php
namespace Loader\Utility\Node;

use essentials\addon\NodeEditor\Node;
use Loader\Utility\Model\SOAPModel;

class SoapClientNode extends Node{
    protected $soap;
    public $in = [
        'Url' => 'url',
        'Username' => 'string',
        'Password' => 'string'
    ];
    public $out = [
        'SoapClient' => 'soapclient'
    ];
    public function method(array $inputs) {
        return ['SoapClient' => new SOAPModel(
            $inputs['Url'],
            $inputs['Username'],
            $inputs['Password']
        )];
    }
}