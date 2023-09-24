<?php
namespace NodeEditor\Node;

require_once __DIR__.'/../../../../../vendor/essentials/addon/XML/XMLModel.php';
use NodeEditor\Utility\Node;
use essentials\addon\XML\XMLModel\XMLModel;

abstract class XMLNode extends Node{
    protected  $xml;
    protected  $element;
    public $in = [
        'File' => [
            'type' => 'filename',
            'connector' => false
        ]
    ];
    function init(array $inputs){
        $this->xml = new XMLModel($inputs['File']);
        $start = $this->getUserData('start');
        $this->element = $this->getUserData('element');
        if(isset($start)){
            $this->xml->stepTo($start);
        }
        $this->xml->step();
    }
    public $edit = [
        'label' => [
            'filter' => FILTER_SANITIZE_STRING
        ],
        'start' => [
            'filter' => FILTER_SANITIZE_STRING
        ],
        'element' => [
            'filter' => FILTER_SANITIZE_STRING
        ]
    ];
    function edit($label, $start = null, $element = null) {
        $this->setUserData('start', $start);
        $this->setUserData('element', $element);
        parent::edit($label);
    }
    public function userViewInit() {
        parent::userViewInit();
        $this->edit['start']['default'] = $this->getUserData('start');
        $this->edit['element']['default'] = $this->getUserData('element');
    }
    public function userView() {
        parent::userView();
        Html::input('start','placeholder="Parent of the element"');
        Html::input('element','placeholder="Element to be looped"');
    }
}