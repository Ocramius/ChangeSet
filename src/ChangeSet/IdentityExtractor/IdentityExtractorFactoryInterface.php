<?php

namespace ChangeSet\IdentityExtractor;

interface IdentityExtractorFactoryInterface
{

    public function getExtractor($className);
}
