<?php
namespace Loader\Utility\Node;
use essentials\addon\NodeEditor\Node;
use Loader\Utility\Model\CacheModel;

class SoapActionNode extends Node{
    public $in = [
        'SoapClient' => [
            'type' => 'soapclient',
            'connector' => false
        ],
        'Timeout' => 'time'
    ];
    protected $action;
    protected $soap;
    protected $stream;
    function init(array $inputs){
        $this->soap = $inputs['SoapClient'];
        $this->action = $this->getUserData('action');
        $this->stream = $this->getUserData('stream');
    }
    public function method(array $inputs) {
        if(isset($this->soap, $this->action)){
            //cache response
            $path = CacheModel::cache($this->soap->getUrl(),"input/{$thisenvironment->id}/$this->name/Data", $inputs['Timeout'], function($source, $target)use($inputs){
                if($this->stream){
                    $this->soap->callStream($target, $this->action, $inputs);
                }else{
                    $this->soap->callSave($target, $this->action, $inputs);
                }
            });
            if($this->stream){
                return ['SoapResponse' => $path];
            }else{
                $result = [];
                $path = substr($path, 0,strrpos($path, '-'));
                foreach($this->out as $key => $out){
                    $result[$key] = "$path-$key";
                }
                return $result;
            }
        }else{
            throw new Exception('Soap or Action not set');
        }
    }
    public function userViewInit() {
        $actions = array_keys($this->soap->getFunctions());
        $this->edit['action']['values'] = array_combine($actions, $actions);
        $this->edit['action']['default'] = $this->action;
        $this->edit['stream']['default'] = $this->stream;
    }

    public function userView() {
        parent::userView();
        Html::input('action');
        Html::input('stream',"id='stream-boolean'");
        echo "<label for='stream-boolean'>Stream</label>";
    }
    public $edit = [
        'label' => [
            'filter' => FILTER_SANITIZE_STRING
        ],
        'action' => [
            'filter' => FILTER_SANITIZE_STRING,
            'type' => 'select',
            'values' => []
        ],
        'stream' => [
            'filter' => FILTER_VALIDATE_BOOLEAN,
            'type' => 'checkbox'
        ]
    ];
    public function edit($label, $action = null,$stream = null) {
        try{
            $thisenvironment->init();
        } catch (Exception $ex) {

        }
        if($action){
            $this->setUserData('label', $label);
            $this->setUserData('action', $action);
            $this->setUserData('stream', $stream);
            $soap_action = $this->soap->getAction($action);
            $this->setIn($soap_action['in']);
            if($this->stream){
                $this->setOut(['SoapResponse' => 'XML']);
            }else{
                $this->setOut($soap_action['out']);
            }
        }
        return parent::edit($label);
    }
}