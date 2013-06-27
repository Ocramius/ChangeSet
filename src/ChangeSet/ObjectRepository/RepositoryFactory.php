<?php

namespace ChangeSet\ObjectRepository;

use ChangeSet\Container\Container;

class RepositoryFactory extends Container implements RepositoryFactoryInterface
{

    public function getObjectRepository($className)
    {
        return $this[$className];
    }
}