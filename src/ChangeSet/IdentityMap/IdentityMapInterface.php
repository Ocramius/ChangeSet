<?php

namespace ChangeSet\IdentityMap;

interface IdentityMapInterface
{
    /** @return bool success */
    public function add($object);

    /** @return bool success */
    public function remove($object);

    public function get($className, $id);

    public function contains($object);
}
