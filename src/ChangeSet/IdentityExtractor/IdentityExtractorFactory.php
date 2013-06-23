<?php

namespace ChangeSet\IdentityExtractor;

// @todo implement collection interfaces?
class IdentityExtractorFactory
{
    public function getExtractor($className)
    {
        return new FakeIdentityExtractor();
    }
}
