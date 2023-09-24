<?php
namespace Loader\Utility\Node;

use Cake\Database\StatementInterface;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use essentials\addon\NodeEditor\DynamicNodeInterface;
use essentials\addon\NodeEditor\Node;
use Iterator;

class ExportInputNode extends Node implements DynamicNodeInterface{
    
    private $exportEntity;
    /**
     * @var Query
     */
    private $carsQuery;
    /**
     * @var StatementInterface 
     */
    private $result;
    
    private $page = 1;
    //Limit one because for the init we need data also.
    private $limit = 1;
    
    public $out = [
        'username' => 'String',
        'externalId' => 'String',
        'mycar' => 'Entity'
    ];


    public function init(array $inputs) {
        $this->exportEntity = $this->editor->exportEntity;
        $where = array_merge(['CarExportMycars.car_export_id' => $this->exportEntity->id], $this->editor->exportCondition ?? []);
        $this->carsQuery = TableRegistry::get('Car.CarMycars')->find()
            ->where($where)
            ->contain([
                'CarManufacturers',
                'CarModels',
                'CarUsers',
                'CarTypes',
                'CarParamsTechnicals',
                'CarMycarSellImages',
                'CarParamsAll',
                'CarParamsSecurities',
                'CarParamsComforts',
                'CarParamsMultimedias',
                'CarParamsOthers',
                'CarExportMycars',
            ])->limit(64)->enableBufferedResults(false);
    }
    
    public function method(array $inputs) {
        $mycar = $this->getNextMycar();
        if($mycar && $this->limit-- > 0){
            return [
                'mycar' => $mycar,
                'username' => $this->exportEntity->username,
                'externalId' => $mycar->car_export_mycar->external_id
            ];
        }else{
            return false;
        }
    }
    
    public function reset() {
        $this->result = null;
        $this->page = 1;
        $this->limit = $this->exportEntity->car_limit;
        parent::reset();
    }
    
    public function getCurrentMycar(){
        return $this->result->current() ?? null;
    }
    
    public function getNextMycar(){
        
        if(isset($this->result)){
            $this->result->next();
        }
        if(isset($this->result) && $this->result->valid()){
            return $this->result->current();
        }else{
            $this->result = $this->carsQuery->page($this->page++)->all();
            if($this->result->valid()){
                return $this->result->current();
            }else{
                return false;
            }
        }
    }
}