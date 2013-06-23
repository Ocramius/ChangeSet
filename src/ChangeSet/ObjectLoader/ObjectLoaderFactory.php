<?php

namespace ChangeSet\ObjectLoader;

use ChangeSet\UnitOfWork\UnitOfWorkInterface;

class ObjectLoaderFactory
{
    private $unitOfWork;

    public function __construct(UnitOfWorkInterface $unitOfWork)
    {
        $this->unitOfWork = $unitOfWork;
    }

    public function getObjectLoader($className)
    {
        return new SimpleObjectLoader($this->unitOfWork);
    }
}
