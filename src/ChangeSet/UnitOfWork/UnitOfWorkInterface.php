<?php

namespace ChangeSet\UnitOfWork;

use ChangeSet\Committer\CommitterInterface;

interface UnitOfWorkInterface
{
    const STATE_UNMANAGED = 0b0001;
    const STATE_MANAGED   = 0b0010;
    const STATE_NEW       = 0b0100;
    const STATE_REMOVED   = 0b1000;

    public function registerClean($object);

    public function registerNew($object);

    public function registerRemoved($object);

    public function commit(CommitterInterface $committer);

    /**
     * @param object $object
     *
     * @return int one of either:
     *              - {@see UnitOfWorkInterface::STATE_UNMANAGED}
     *              - {@see UnitOfWorkInterface::STATE_MANAGED}
     *              - {@see UnitOfWorkInterface::STATE_NEW}
     *              - {@see UnitOfWorkInterface::STATE_REMOVED}
     */
    public function getState($object);

    // @TODO rollback?
}
