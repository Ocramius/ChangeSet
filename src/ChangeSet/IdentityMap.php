<?php

namespace ChangeSet;

use ChangeSet\IdentityExtractor\IdentityExtractorFactory;

// @todo implement collection interfaces?
class IdentityMap
{
    private $map = array();
    private $identityExtractorFactory;

    public function __construct()
    {
        $this->identityExtractorFactory = new IdentityExtractorFactory();
    }

    public function add($object)
    {
        // @todo should the ID be checked for validity here? (non-null IDs)
        $id = $this
            ->identityExtractorFactory
            ->getExtractor(get_class($object))
            ->extractIdentityHash($object);

        $this->map[$id] = $object;
    }

    public function remove($object)
    {
        $id = $this
            ->identityExtractorFactory
            ->getExtractor(get_class($object))
            ->extractIdentityHash($object);

        unset($this->map[$id]);
    }

    public function get($className, $id)
    {
        // @todo reuse the identity extractor here!
        $hash = $this
            ->identityExtractorFactory
            ->getExtractor($className)
            ->hashIdentifier($id);

        return isset($this->map[$hash]) ? $this->map[$hash] : null;
    }

    public function getId($object)
    {
        return $this
            ->identityExtractorFactory
            ->getExtractor(get_class($object))
            ->extractIdentity($object);
    }
}
