<?php

namespace ChangeSet\ObjectLoader;

use ChangeSet\UnitOfWork\UnitOfWorkInterface;

class SimpleObjectLoader implements ObjectLoaderInterface
{
    private $unitOfWork;
    public function __construct(UnitOfWorkInterface $unitOfWork)
    {
        $this->unitOfWork = $unitOfWork;
    }

    public function loadObject($className, $id)
    {
        $loaded = $this->doFakeLoading($id);

        $this->unitOfWork->registerClean($loaded);

        return $loaded;
    }

    // @todo handle weak links in here? (proxies) - fetch loaders for other entities?
    // @todo handle collection eager/lazy loading here? etc etc...
    public function doFakeLoading($id)
    {
        $object = new \stdClass();

        $object->identity = $id;
        $object->foo = uniqid('foo', true);
        $object->bar = uniqid('bar', true);

        return $object;
    }
}
