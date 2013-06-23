<?php

namespace ChangeSet\IdentityMap;

use ChangeSet\IdentityExtractor\IdentityExtractorFactory;

interface IdentityMapInterface
{
	/** @return bool success */
    public function add($object);

	/** @return bool success */
    public function remove($object);

    public function get($className, $id);
}
