<?php

namespace ChangeSet\IdentityMap;

use ChangeSet\IdentityExtractor\IdentityExtractorFactoryInterface;
use SplObjectStorage;

// @todo implement collection interfaces?
class IdentityMap implements IdentityMapInterface
{
    private $map = array();
    private $objects;
    private $identityExtractorFactory;

    public function __construct(IdentityExtractorFactoryInterface $IdentityExtractorFactory)
    {
        $this->identityExtractorFactory = $IdentityExtractorFactory;
        $this->objects = new SplObjectStorage();
    }

    public function add($object)
    {
        // @todo should the ID be checked for validity here? (non-null IDs)
        $id = $this
            ->identityExtractorFactory
            ->getExtractor(get_class($object))
            ->getEncodedIdentifier($object);

        if (null !== $id) {
            $success = !isset($this->map[$id]);
            $this->map[$id] = $object;
            $this->objects[$object] = $id;

            return $success;
        }

        return false;
    }

    public function remove($object)
    {
        if (!isset($this->objects[$object])) {
            return false;
        }

        unset($this->map[$this->objects->offsetGet($object)], $this->objects[$object]);

        return true;
    }

    public function get($className, $id)
    {
        $hash = $this
            ->identityExtractorFactory
            ->getExtractor($className)
            ->encodeIdentifier($id);

        return ((null !== $hash) && isset($this->map[$hash])) ? $this->map[$hash] : null;
    }

    public function getId($object)
    {
        if (isset($this->objects[$object])) {
            return $this->objects->offsetGet($object);
        }

        throw new \InvalidArgumentException('I NO HAZ ZIS');
    }

    public function contains($object)
    {
        return isset($this->objects[$object]);
    }
}
