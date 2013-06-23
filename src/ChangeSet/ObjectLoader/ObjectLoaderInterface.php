<?php

namespace ChangeSet\ObjectLoader;

interface ObjectLoaderInterface
{
    public function loadObject($id, $className);
}
