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

    private $identifiersByObjectHashMap = array();
    private $objectsByIdentifierMap     = array();

    public function add($object, $identifier = null)
    {
        $oid = spl_object_hash($object);

        if (isset($this->identifiersByObjectHashMap[$oid])) {
            return false;
        }

        $class = get_class($object); // @todo introduce something to resolve the correct class name instead, as `get_class` is too naive

        if (null === $identifier) {
            $computedIdentifier = $object->identifier;  // @todo introduce identity extractor/hasher here

            $this->identifiersByObjectHashMap[$oid]                                                = $computedIdentifier;
            $this->identifiersByObjectHashMap[$oid]                                                = $computedIdentifier;
            $this->objectsByIdentifierMap[$class . self::IDENTITY_DELIMITER . $computedIdentifier] = $object;

            return true; // @todo add different return type for already present values
        }


        // @todo hash the identifier here
        $this->identifiersByObjectHashMap[$oid]                                                = $identifier;
        $this->objectsByIdentifierMap[$class . self::IDENTITY_DELIMITER . $identifier] = $object;

        return true; // @todo add different return type for already present values
    }

    public function removeObject($object)
    {
        $oid = spl_object_hash($object);

        if (! isset($this->identifiersByObjectHashMap[$oid])) {
            return false;
        }

        // @todo hash the identifier here
        $identifier = $this->identifiersByObjectHashMap[$oid];

        unset(
            $this->objectsByIdentifierMap[get_class($object) . self::IDENTITY_DELIMITER . $identifier],
            $this->identifiersByObjectHashMap[$oid]
        );

        return true;
    }

    public function removeByIdentifier($className, $identifier)
    {
        // @todo hash the identifier here
        $identifierIndex = $className . self::IDENTITY_DELIMITER . $identifier;

        if (! isset($this->objectsByIdentifierMap[$identifierIndex])) {
            return false;
        }

        $oid = spl_object_hash($this->objectsByIdentifierMap[$identifierIndex]);

        unset(
            $this->objectsByIdentifierMap[$identifierIndex],
            $this->identifiersByObjectHashMap[$oid]
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
        return isset($this->identifiersByObjectHashMap[spl_object_hash($object)]);
    }

    /**
     * @param object $object
     *
     * @return mixed
     */
    public function getIdentity($object)
    {
        return $this->identifiersByObjectHashMap[spl_object_hash($object)];
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
