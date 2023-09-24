<?php
namespace Loader\Utility\Node;
use essentials\addon\NodeEditor\Node;

class QueryConvertNode extends Node{

    public $in = [
        'input' => 'Entity'
    ];

    public $out = [
        'result' => 'String'
    ];

    public $map = [];

    public $keyField = 'id';
    public $valueField = 'name';

    public function init(array $inputs) {
        parent::init($inputs);
        if(is_array($inputs['input'])){
            $entity = $inputs['input'][0] ?? null;
        }else{
            $entity = $inputs['input'] ?? null;
        }
        if(isset($entity) && empty($this->getUserData('model'))){
            $this->setUserData('model', $entity->source());
        }
        $this->map = $this->getUserData('map');
    }

    public function method(array $inputs) {
        if(is_array($inputs['input'])){
            $result = [];
            foreach($inputs['input'] as $input){
                if($input instanceof \Cake\ORM\Entity){
                    $value = $input->get($this->keyField) ?? null;
                }else{
                    $value = $input;
                }
                if(!empty($value) && !empty($this->map[$value])){
                    $result []= $this->map[$value];
                }
            }
            return compact('result');
        }else{
            if($inputs['input'] instanceof \Cake\ORM\Entity){
                $value = $inputs['input']->get($this->keyField) ?? null;
            }else{
                $value = $inputs['input'];
            }
            if(!empty($value)){
                return ['result' => $this->map[$value] ?? null];
            }
        }
        return ['result' => null];
    }

}
