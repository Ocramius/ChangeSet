<?php

namespace ChangeSet\IdentityMap;

use ChangeSet\IdentityExtractor\IdentityExtractorFactory;

// @todo implement collection interfaces?
class IdentityMap implements IdentityMapInterface
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
            ->getEncodedIdentifier($object);

        if (null !== $id) {
			$this->map[$id] = $object;
		}
    }

    public function remove($object)
    {
        $id = $this
            ->identityExtractorFactory
            ->getExtractor(get_class($object))
			->getEncodedIdentifier($object);

        if (null !== $id) {
			$this->map[$id] = $object;
		}
		
        unset($this->map[$id]);
    }

    public function get($className, $id)
    {
        // @todo reuse the identity extractor here!
        $hash = $this
            ->identityExtractorFactory
            ->getExtractor($className)
            ->encodeIdentifier($id);
		
        return ((null !== $hash) && isset($this->map[$hash])) ? $this->map[$hash] : null;
    }

    public function getId($object)
    {
        return $this
            ->identityExtractorFactory
            ->getExtractor(get_class($object))
            ->getIdentity($object);
    }
}
