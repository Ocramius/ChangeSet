<?php

namespace ChangeSet\ObjectRepository;

use ChangeSet\UnitOfWork\UnitOfWorkInterface;
use ChangeSet\ObjectLoader\ObjectLoaderInterface;
use ChangeSet\IdentityMap\IdentityMapInterface;

class SimpleObjectRepository implements ObjectRepositoryInterface
{
    protected $unitOfWork;
    protected $objectLoader;
    protected $identityMap;
    public function __construct(
        UnitOfWorkInterface $unitOfWork,
        ObjectLoaderInterface $objectLoader,
        IdentityMapInterface $identityMap
    ) {
        $this->unitOfWork = $unitOfWork;
        $this->objectLoader = $objectLoader;
        $this->identityMap = $identityMap;
    }

    public function add($object)
    {
        $this->unitOfWork->registerNew($object);
    }

    public function remove($object)
    {
        $this->unitOfWork->registerRemoved($object);

        return true;
    }

    public function get($id)
    {
        if ($object = $this->identityMap->get('stdClass', $id)) {
            return $object;
        }

        $object = $this->objectLoader->loadObject('stdClass', $id);

        return $object;
    }

    public function getReference($id)
    {
        return $this->get($id);
    }
}
