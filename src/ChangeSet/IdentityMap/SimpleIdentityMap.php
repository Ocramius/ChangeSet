<?php

namespace ChangeSet\IdentityMap;
use ChangeSet\IdentityExtractor\IdentityExtractorInterface;
use ChangeSet\TypeResolver\TypeResolverInterface;

/**
 * @TODO consider bulk API
 */
final class SimpleIdentityMap
{
    const IDENTITY_DELIMITER = '#';

    /**
     * @var string[] indexed by object identifier
     */
    private $identitiesByObjectHashMap = array();

    /**
     * @var object[] indexed by hashed identity
     */
    private $objectsByIdentityMap     = array();

    /**
     * @var IdentityExtractorInterface
     */
    private $identityExtractor;

    public function __construct(IdentityExtractorInterface $identityExtractor, TypeResolverInterface $typeResolver)
    {
        $this->identityExtractor = $identityExtractor;
        $this->typeResolver      = $typeResolver;
    }

    public function add($object, $identity = null)
    {
        $oid = spl_object_hash($object);

        if (isset($this->identitiesByObjectHashMap[$oid])) {
            return false;
        }

        $type = $this->typeResolver->getTypeOfObject($object);

        if (null === $identity) {
            $encodedIdentity  = $this->identityExtractor->getEncodedIdentifier($object);
            $computedIdentity = $this->identityExtractor->getIdentity($object);

            $this->identitiesByObjectHashMap[$oid]                                           = $computedIdentity;
            $this->objectsByIdentityMap[$type . self::IDENTITY_DELIMITER . $encodedIdentity] = $object;

            return true;
        }

        $encodedIdentity = $this->identityExtractor->encodeIdentifier($identity);

        $this->identitiesByObjectHashMap[$oid]                                           = $identity;
        $this->objectsByIdentityMap[$type . self::IDENTITY_DELIMITER . $encodedIdentity] = $object;

        return true;
    }

    public function removeObject($object)
    {
        $oid = spl_object_hash($object);

        if (! isset($this->identitiesByObjectHashMap[$oid])) {
            return false;
        }

        $identity = $this->identitiesByObjectHashMap[$oid];

        unset(
            $this->objectsByIdentityMap[$this->typeResolver->getTypeOfObject($object) . self::IDENTITY_DELIMITER . $identity],
            $this->identitiesByObjectHashMap[$oid]
        );

        return true;
    }

    public function removeByIdentity($className, $identity)
    {
        $identityIndex = $this->typeResolver->resolveType($className) . self::IDENTITY_DELIMITER . $identity;

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
        $identityIndex = $this->typeResolver->resolveType($className)
            . self::IDENTITY_DELIMITER
            . $this->identityExtractor->encodeIdentifier($identity);

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
            $this->typeResolver->resolveType($className)
            . self::IDENTITY_DELIMITER
            . $this->identityExtractor->encodeIdentifier($identity)
        ]);
    }
}
