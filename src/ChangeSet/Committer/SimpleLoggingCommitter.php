<?php

namespace ChangeSet\Committer;

use ChangeSet\ChangeTracking\ChangeMap;

class SimpleLoggingCommitter implements CommitterInterface
{
    public $operations = array();

    public function commit(ChangeMap $changeSet)
    {
        $this->operations = array();

        foreach ($changeSet->getNew() as $insert) {
            $this->addChange('insert', $insert->getObject());
        }

        foreach ($changeSet->getChangedManaged() as $update) {
            $this->addChange('update', $update->getObject());
        }

        foreach ($changeSet->getRemoved() as $delete) {
            $this->addChange('delete', $delete->getObject());
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
