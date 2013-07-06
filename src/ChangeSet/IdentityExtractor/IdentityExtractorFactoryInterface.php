<?php

namespace ChangeSet\IdentityExtractor;

Interface IdentityExtractorFactoryInterface
{

    public function getExtractor($className);
}