<?php

namespace ChangeSet\IdentityExtractor;

interface IdentityExtractorInterface
{
    /** @param object $object @return string */
    public function getType($object);

    /** @return mixed|null */
    public function getIdentity($object);

    /** @return string|null */
    public function getEncodedIdentifier($object);

    /** @return string|null */
    public function encodeIdentifier($identifier);
}
