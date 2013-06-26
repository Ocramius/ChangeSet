<?php

namespace ChangeSet\ObjectRepository;


class SimpleObjectRepository extends ObjectRepository implements ObjectRepositoryInterface
{
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
