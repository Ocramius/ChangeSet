<?php

namespace ChangeSet\IdentityMap;

/**
 * @TODO consider bulk API
 */
class SimpleIdentityMap
{
    const IDENTITY_DELIMITER = '#';

    private $identitysByObjectHashMap = array();
    private $objectsByIdentityMap     = array();

    public function add($object, $identity = null)
    {
        $oid = spl_object_hash($object);

        if (isset($this->identitysByObjectHashMap[$oid])) {
            return false;
        }

        $class = get_class($object); // @todo introduce something to resolve the correct class name instead, as `get_class` is too naive

        if (null === $identity) {
            $computedIdentity = $object->identity;  // @todo introduce identity extractor/hasher here

            $this->identitysByObjectHashMap[$oid]                                                = $computedIdentity;
            $this->identitysByObjectHashMap[$oid]                                                = $computedIdentity;
            $this->objectsByIdentityMap[$class . self::IDENTITY_DELIMITER . $computedIdentity] = $object;

            return true; // @todo add different return type for already present values
        }


        // @todo hash the identity here
        $this->identitysByObjectHashMap[$oid]                                                = $identity;
        $this->objectsByIdentityMap[$class . self::IDENTITY_DELIMITER . $identity] = $object;

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
            $this->objectsByIdentityMap[get_class($object) . self::IDENTITY_DELIMITER . $identity],
            $this->identitysByObjectHashMap[$oid]
        );

        return true;
    }

    public function removeByIdentity($className, $identity)
    {
        // @todo hash the identity here
        $identityIndex = $className . self::IDENTITY_DELIMITER . $identity;

        if (! isset($this->objectsByIdentityMap[$identityIndex])) {
            return false;
        }

        $oid = spl_object_hash($this->objectsByIdentityMap[$identityIndex]);

        unset(
            $this->objectsByIdentityMap[$identityIndex],
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
        $identityIndex = $className . self::IDENTITY_DELIMITER . $identity;

        return isset($this->objectsByIdentityMap[$identityIndex]) ? $this->objectsByIdentityMap[$identityIndex] : null;
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
        $oid = spl_object_hash($object);

        return isset($this->identitysByObjectHashMap[$oid]) ? $this->identitysByObjectHashMap[$oid] : null;
    }

    /**
     * @param mixed $identity
     *
     * @return bool
     */
    public function hasIdentity($className, $identity)
    {
        return isset($this->objectsByIdentityMap[$className . self::IDENTITY_DELIMITER . $identity]);
    }
}
