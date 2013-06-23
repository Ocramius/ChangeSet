<?php

namespace ChangeSet\ObjectManager;

use ChangeSet\ObjectRepository\ObjectRepositoryFactory;

class SimpleObjectManager implements ObjectManagerInterface
{
    protected $objectRepositoryFactory;

    public function __construct(ObjectRepositoryFactory $objectRepositoryFactory)
    {
        $this->objectRepositoryFactory = $objectRepositoryFactory;
    }

    public function getRepository($className)
    {
        return $this->objectRepositoryFactory->getObjectRepository($className);
    }

    public function flush()
    {
    }

    public function clear()
    {
    }
}
