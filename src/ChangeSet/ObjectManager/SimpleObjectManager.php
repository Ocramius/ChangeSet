<?php

namespace ChangeSet\ObjectManager;

use ChangeSet\ObjectRepository\RepositoryFactoryInterface;

class SimpleObjectManager implements ObjectManagerInterface
{
    protected $repositoryFactory;

    public function __construct(RepositoryFactoryInterface $objectRepositoryFactory)
    {
        $this->repositoryFactory = $objectRepositoryFactory;
    }

    public function getRepository($className)
    {
        return $this->repositoryFactory->getObjectRepository($className);
    }

    public function flush()
    {
    }

    public function clear()
    {
    }
}
