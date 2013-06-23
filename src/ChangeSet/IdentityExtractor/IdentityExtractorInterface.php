<?php

namespace ChangeSet\IdentityExtractor;

interface IdentityExtractorInterface
{
	/** @return mixed|null */
    public function getIdentity($object);
	
	/** @return string|null */
    public function getEncodedIdentifier($object);

	/** @return string|null */
    public function encodeIdentifier($identifier);
}
