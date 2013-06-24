<?php

namespace ChangeSet\Committer;

use ChangeSet\ChangeSet;

class SimpleLoggingCommitter implements CommitterInterface
{
    public $operations = array();
    public function commit(ChangeSet $changeSet)
    {
        $this->operations = array();
        
        foreach ($changeSet->getNew() as $insert) {
            $this->addChange('insert', $insert); 
        }
        
        foreach ($changeSet->getChangedManaged() as $update) {
            $this->addChange('update', $update); 
        }
        
        foreach ($changeSet->getRemoved() as $delete) {
            $this->addChange('delete', $delete); 
        }
    }
    
    private function addChange($type, $object)
    {
        $this->operations[] = array(
            'type' => $type,
            'object' => $object,
        );
    }
}