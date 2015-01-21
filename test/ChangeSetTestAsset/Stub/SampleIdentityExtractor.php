<?php

namespace ChangeSetTestAsset\Stub;

use ChangeSet\IdentityExtractor\IdentityExtractorInterface;

/**
 * Sample identity extractor: handles identity of `stdClass` objects in an opinionated way
 */
class SampleIdentityExtractor implements IdentityExtractorInterface
{
    /**
     * {@inheritDoc}
     */
    public function getIdentity($object)
    {
        if (! $object instanceof \stdClass) {
            throw new \RuntimeException(sprintf(
                'Cannot extract identity from object of type %s',
                is_object($object) ? get_class($object) : gettype($object)
            ));
        }

        if (property_exists($object, 'identity')) {
            /*if ($object->identity instanceof \stdClass) {
                return $this->getIdentity($object->identity); // identity depends on another object's identity
            }*/

            /*if (is_object($object->identity)) {
                return spl_object_hash($object->identity); // identity is an object (not yet handled)
            }*/

            return $object->identity;
        }

        $identity = (array) $object;

        if (empty($identity)) {
            return null;
        }

        return $identity;
    }

    /**
     * {@inheritDoc}
     */
    public function getEncodedIdentifier($object)
    {
        return $this->encodeIdentifier($this->getIdentity($object));
    }

    /**
     * {@inheritDoc}
     */
    public function encodeIdentifier($identifier)
    {
        if (is_object($identifier)) {
            return spl_object_hash($identifier);
        }

        return implode('|', (array) $identifier);
    }
}
