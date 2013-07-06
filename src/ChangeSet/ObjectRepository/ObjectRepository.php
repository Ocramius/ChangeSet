<?php

namespace ChangeSet\ObjectRepository;

use ChangeSet\UnitOfWork\UnitOfWorkInterface;
use ChangeSet\ObjectLoader\ObjectLoaderInterface;
use ChangeSet\IdentityMap\IdentityMapInterface;
use ChangeSet\ObjectRepository\ObjectRepositoryInterface;

abstract class ObjectRepository implements ObjectRepositoryInterface
{
    protected $unitOfWork;
    protected $objectLoader;
    protected $identityMap;
    protected $entityClassName;

    public function __construct(
        UnitOfWorkInterface $unitOfWork,
        ObjectLoaderInterface $objectLoader,
        IdentityMapInterface $identityMap,
        $entityClassName
    )
    {
        $this->unitOfWork = $unitOfWork;
        $this->objectLoader = $objectLoader;
        $this->identityMap = $identityMap;
        $this->entityClassName = $entityClassName;
    }
}