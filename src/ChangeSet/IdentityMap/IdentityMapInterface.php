<?php

namespace ChangeSet\IdentityMap;

use ChangeSet\IdentityExtractor\IdentityExtractorFactory;

interface IdentityMapInterface
{
    public function add($object);

    public function remove($object);

    public function get($className, $id);
}
