<?php

namespace ChangeSet\IdentityExtractor;

class SimpleIdentityExtractor implements IdentityExtractorInterface
{
    public function getIdentity($object)
    {
        return $object->identity;
    }

    public function getEncodedIdentifier($object)
    {
        return $this->encodeIdentifier($this->getIdentity($object));
    }
	
	public function encodeIdentifier($identifier)
	{
		return null === $identifier ? null : 'stdClass@' . $identifier;
	}
}
