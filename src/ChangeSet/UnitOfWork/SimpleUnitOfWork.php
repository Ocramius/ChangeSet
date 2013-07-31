<?php

namespace ChangeSet\UnitOfWork;

use ChangeSet\ChangeTracking\ChangeMap;
use ChangeSet\Committer\CommitterInterface;

class SimpleUnitOfWork implements UnitOfWorkInterface
{
    protected $changeSet;

    public function __construct(ChangeMap $changeSet)
    {
        $this->changeSet = $changeSet;
    }

    public function registerClean($object)
    {
        $this->changeSet->register($object);
    }

    public function registerNew($object)
    {
        $this->changeSet->add($object);
    }

    public function registerRemoved($object)
    {
        $this->changeSet->remove($object);
    }

    public function commit(CommitterInterface $committer)
    {
        $committer->commit($this->changeSet);

        $this->changeSet = $this->changeSet->clean();
    }
}
