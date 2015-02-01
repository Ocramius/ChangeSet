<?php

namespace ChangeSetTestAsset\Stub;

use ChangeSet\TypeResolver\TypeResolverInterface;

final class SampleTypeResolver implements TypeResolverInterface
{
    /**
     * @var string[]
     */
    private $subTypesMap;

    /**
     * @param $subTypesMap string[]
     */
    public function __construct(array $subTypesMap)
    {
        $this->subTypesMap = array_map(
            function ($type) {
                return (string) $type;
            },
            $subTypesMap
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getTypeOfObject($object)
    {
        return $this->resolveType(get_class($object));
    }

    /**
     * {@inheritDoc}
     */
    public function resolveType($type)
    {
        return isset($this->subTypesMap[$type]) ? $this->subTypesMap[$type] : $type;
    }
}