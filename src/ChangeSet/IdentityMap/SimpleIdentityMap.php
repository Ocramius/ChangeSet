<?php

namespace ChangeSet\IdentityMap;
use ChangeSet\IdentityExtractor\IdentityExtractorInterface;

/**
 * @TODO consider bulk API
 */
class SimpleIdentityMap
{
    const IDENTITY_DELIMITER = '#';

    private $identitiesByObjectHashMap = array();
    private $objectsByIdentityMap     = array();

    /**
     * @var IdentityExtractorInterface
     */
    private $identityExtractor;

    public function __construct(IdentityExtractorInterface $identityExtractor)
    {
        $this->identityExtractor = $identityExtractor;
    }

    public function add($object, $identity = null)
    {
        $oid = spl_object_hash($object);

        if (isset($this->identitiesByObjectHashMap[$oid])) {
            return false;
        }

        $type = $this->identityExtractor->getType($object);

        if (null === $identity) {
            $encodedIdentity  = $this->identityExtractor->getEncodedIdentifier($object);
            $computedIdentity = $this->identityExtractor->getIdentity($object);

            $this->identitiesByObjectHashMap[$oid]                                           = $computedIdentity;
            $this->objectsByIdentityMap[$type . self::IDENTITY_DELIMITER . $encodedIdentity] = $object;

            return true; // @todo add different return type for already present values
        }

        $encodedIdentity = $this->identityExtractor->encodeIdentifier($identity);

        $this->identitiesByObjectHashMap[$oid]                                           = $identity;
        $this->objectsByIdentityMap[$type . self::IDENTITY_DELIMITER . $encodedIdentity] = $object;

        return true; // @todo add different return type for already present values
    }

    public function removeObject($object)
    {
        $oid = spl_object_hash($object);

        if (! isset($this->identitiesByObjectHashMap[$oid])) {
            return false;
        }

        // @todo hash the identity here
        $identity = $this->identitiesByObjectHashMap[$oid];

        unset(
            $this->objectsByIdentityMap[get_class($object) . self::IDENTITY_DELIMITER . $identity],
            $this->identitiesByObjectHashMap[$oid]
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
            $this->identitiesByObjectHashMap[$oid]
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
        $identityIndex = $className . self::IDENTITY_DELIMITER . $this->identityExtractor->encodeIdentifier($identity);

        return isset($this->objectsByIdentityMap[$identityIndex]) ? $this->objectsByIdentityMap[$identityIndex] : null;
    }

    /**
     * @param object $object
     *
     * @return bool
     */
    public function hasObject($object)
    {
        return isset($this->identitiesByObjectHashMap[spl_object_hash($object)]);
    }

    /**
     * @param object $object
     *
     * @return mixed
     */
    public function getIdentity($object)
    {
        $oid = spl_object_hash($object);

        return isset($this->identitiesByObjectHashMap[$oid]) ? $this->identitiesByObjectHashMap[$oid] : null;
    }

    /**
     * @param string $className
     * @param mixed  $identity
     *
     * @return bool
     */
    public function hasIdentity($className, $identity)
    {
        return isset($this->objectsByIdentityMap[
            $className . self::IDENTITY_DELIMITER . $this->identityExtractor->encodeIdentifier($identity)
        ]);
    }
}
