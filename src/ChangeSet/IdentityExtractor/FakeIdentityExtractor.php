<?php

namespace ChangeSet\IdentityExtractor;

// @todo implement collection interfaces?
class FakeIdentityExtractor
{
    public function extractIdentity($object)
    {
        return $object->identity;
    }

    public function hashIdentifier($identifier)
    {
        return 'stdClass@' . $identifier;
    }

    public function extractIdentityHash($object)
    {
        return $this->hashIdentifier($object->identity);
    }

    public function hydrateIdentity($object, $identity)
    {
        $object->identity = $identity;
    }
}
