<?php
namespace Loader\Utility\Model;
require_once 'StreamSoapClient.php';
/**
 * Description of SoapModel
 *
 * @author Iván
 */
class SOAPModel {
    protected $url;
    /**@var SoapClient*/
    protected $client;
    protected $functions;
    protected $types;
    protected $actions;
    protected $stream;
    
    function __construct($url, $username = null, $password = null){
        set_time_limit (600);
        $this->url = $url;
        $this->client = new StreamSoapClient($url,[
            'login' => $username, 
            'password' => $password,
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
            //'connection_timeout' => 600
        ]);
    }
    function getTypes(){
        if(isset($this->types)){
            return $this->types;
        }
        $types = $this->client->__getTypes();
        foreach ($types as $type){
            strtok($type, ' ');
            $name = strtok(' {');
            $i = 0;
            while ($tok = strtok(" ;{}\n")){
                $var[$i++%2] = $tok;
                if($i%2 == 0){
                    $this->types[$name][$var[1]] = $var[0];
                }
            }
        }
        return $this->types;
    }
    /**
     * This will return only the input and output names without extraction
     * @return array The functions
     */
    function getFunctions(){
        if(isset($this->functions)){
            return $this->functions;
        }
        $functions = $this->client->__getFunctions();
        foreach($functions as $function){
            $out = strtok($function, ' ');
            $name = strtok('(');
            $in = strtok(' ');
            $this->functions[$name] = [
                'in' => $in,
                'out' => $out
            ];
        }
        return $this->functions;
    }
    /**
     * This function will return the avaliable function with extracted inputs
     * and outputs.
     * @return array Functions with extracted types
     */
    function getActions(){
        if(isset($this->actions)){
            return $this->actions;
        }
        $functions = $this->getFunctions();
        $types = $this->getTypes();
        foreach ($functions as &$function){
            $next = $t = $function['in'];
            while(isset($types[$next])){
                $t = $types[$next];
                $next = is_array($t) ? key($t) : $t;
            }
            $function['in'] = is_array($t) ? $t : null;
            
            $next = $t = $function['out'];
            while(isset($types[$next])){
                $t = $types[$next];
                $next = is_array($t) ? key($t) : $t;
            }
            $function['out'] = is_array($t) ? $t : null;
        }
        return $this->actions = $functions;
    }
    function callReturn($function, $arguments){
        return $this->client->__soapCall($function, ['parameters'=>$arguments]);
    }
    function callSave($filepath, $function, $arguments){
        $result = $this->client->__soapCall($function, ['parameters'=>$arguments]);
        if(isset($result)){
            $result = reset($this->client->__soapCall($function, ['parameters'=>$arguments]));
            foreach ($result as $key => $value){
                file_put_contents("$filepath-$key", $value,LOCK_EX);
            }
        }
    }
    function callStream($filepath, $function, $arguments){
        $this->client->__soapCall($function, ['parameters'=>$arguments],['file' => $filepath]);
    }
    function getAction($name){
        if(!isset($this->actions)){
            $this->getActions();
        }
        return $this->actions[$name];
    }
    function getUrl(){
        return $this->url;
    }
}
