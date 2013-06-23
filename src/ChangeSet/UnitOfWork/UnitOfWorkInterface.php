<?php

namespace ChangeSet\UnitOfWork;

interface UnitOfWorkInterface
{
    public function registerClean($object);

    public function registerNew($object);

    public function registerRemoved($object);

    public function commit();

    // @TODO rollback?
}
