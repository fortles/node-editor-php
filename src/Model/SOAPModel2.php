<?php
namespace Loader\Utility\Model;
require_once 'essentials/addon/XML/XMLModel.php';
class SoapModel {
    /**
     * @var SoapClient Client
     */
    protected $reader;
    protected $url;
    function SoapModel($url, $username, $password, $encoding = null){
        $this->url = $url;
    }
    function getFunctions(){
        $xml = new XMLModel($this->url);
        //Get types
        $types = [];
        $xml->stepTo('s:element');
        while($xml->valid()){
            $name = $xml->getAttribute('name');
            $xml->stepTo('s:element');
            while($xml->valid()){
               $types[$name] []= $xml->getAttributes();
               $xml->next();
            }
            $xml->next();
        }
        //Get messages
        $messages = [];
        $xml->stepTo('wsdl:message');
        while($xml->valid()){
            $name = $xml->getAttribute('name');
            $xml->step();
            $messages[$name] = $xml->getAttribute('element');
            $xml->next();
        }
        //Get operations


        var_dump($messages);
        //Get messages
        
    }
}
