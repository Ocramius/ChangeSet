<?php

namespace ChangeSet\TypeResolver;

interface TypeResolverInterface
{
    /**
     * @param $object
     *
     * @return string
     */
    public function getTypeOfObject($object);

    /**
     * @param string $type
     *
     * @return string
     */
    public function resolveType($type);
}