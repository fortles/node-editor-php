<?php
namespace NodeEditor;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use NodeEditor\Model\Entity\NodeEditor;
use NodeEditor\Model\Table\NodeEditorTable;


class EntityNodeEnvironment extends NodeEnvironment{
    
    /**
     * @var NodeEditorTable
     */
    private $NodeEditor;
    private $entity;
    
    public function __construct(NodeEditor $nodeEditor) {
        $this->types = Configure::read('NodeEditor.types');
        $this->NodeEditor = TableRegistry::getTableLocator()->get('NodeEditor.NodeEditor');
        $this->entity = $nodeEditor;
    }
    
    protected function getData(): string {
        return $this->entity->node;
    }

    protected function setData(string $data): void {
        $this->entity->node = $data;
        $this->NodeEditor->save($this->entity);
    }

}
