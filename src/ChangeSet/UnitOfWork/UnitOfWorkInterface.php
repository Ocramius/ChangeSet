<?php

namespace ChangeSet\UnitOfWork;

use ChangeSet\Committer\CommitterInterface;

interface UnitOfWorkInterface
{
    public function registerClean($object);

    public function registerNew($object);

    public function registerRemoved($object);

    public function commit(CommitterInterface $committer);

    // @TODO rollback?
}
