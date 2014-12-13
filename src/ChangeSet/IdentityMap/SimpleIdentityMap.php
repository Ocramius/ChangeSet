<?php

namespace ChangeSet\IdentityMap;

use ChangeSet\IdentityExtractor\IdentityExtractorFactoryInterface;
use SplObjectStorage;

/**
 * @TODO consider bulk API
 */
class SimpleIdentityMap
{
    const IDENTITY_DELIMITER = '#';

    private $identitysByObjectHashMap = array();
    private $objectsByIdentifierMap     = array();

    public function add($object, $identity = null)
    {
        $oid = spl_object_hash($object);

        if (isset($this->identitysByObjectHashMap[$oid])) {
            return false;
        }

        $class = get_class($object); // @todo introduce something to resolve the correct class name instead, as `get_class` is too naive

        if (null === $identity) {
            $computedIdentifier = $object->identity;  // @todo introduce identity extractor/hasher here

            $this->identitysByObjectHashMap[$oid]                                                = $computedIdentifier;
            $this->identitysByObjectHashMap[$oid]                                                = $computedIdentifier;
            $this->objectsByIdentifierMap[$class . self::IDENTITY_DELIMITER . $computedIdentifier] = $object;

            return true; // @todo add different return type for already present values
        }


        // @todo hash the identity here
        $this->identitysByObjectHashMap[$oid]                                                = $identity;
        $this->objectsByIdentifierMap[$class . self::IDENTITY_DELIMITER . $identity] = $object;

        return true; // @todo add different return type for already present values
    }

    public function removeObject($object)
    {
        $oid = spl_object_hash($object);

        if (! isset($this->identitysByObjectHashMap[$oid])) {
            return false;
        }

        // @todo hash the identity here
        $identity = $this->identitysByObjectHashMap[$oid];

        unset(
            $this->objectsByIdentifierMap[get_class($object) . self::IDENTITY_DELIMITER . $identity],
            $this->identitysByObjectHashMap[$oid]
        );

        return true;
    }

    public function removeByIdentifier($className, $identity)
    {
        // @todo hash the identity here
        $identityIndex = $className . self::IDENTITY_DELIMITER . $identity;

        if (! isset($this->objectsByIdentifierMap[$identityIndex])) {
            return false;
        }

        $oid = spl_object_hash($this->objectsByIdentifierMap[$identityIndex]);

        unset(
            $this->objectsByIdentifierMap[$identityIndex],
            $this->identitysByObjectHashMap[$oid]
        );

        return true;
    }

    /**
     * @param string $className
     * @param mixed  $identity
     *
     * @return object|null
     */
    public function getObject($className, $identity)
    {
        return $this->objectsByIdentifierMap[$className . self::IDENTITY_DELIMITER . $identity];
    }

    /**
     * @param object $object
     *
     * @return bool
     */
    public function hasObject($object)
    {
        return isset($this->identitysByObjectHashMap[spl_object_hash($object)]);
    }

    /**
     * @param object $object
     *
     * @return mixed
     */
    public function getIdentity($object)
    {
        return $this->identitysByObjectHashMap[spl_object_hash($object)];
    }

    /**
     * @param mixed $identity
     *
     * @return bool
     */
    public function hasIdentity($className, $identity)
    {
        return isset($this->objectsByIdentifierMap[$className . self::IDENTITY_DELIMITER . $identity]);
    }
}
