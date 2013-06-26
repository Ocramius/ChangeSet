<?php

namespace ChangeSet\IdentityExtractor;

use ChangeSet\Container\Container;

class IdentityExtractorFactory extends Container implements IdentityExtractorFactoryInterface
{

    public function getExtractor($className)
    {
        return $this[$className];
    }
}