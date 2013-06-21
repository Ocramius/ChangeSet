<?php

namespace ChangeSet;

class Change
{
    private $object;
    private $snapshot;
    public function __construct($object, $compute = false)
    {
        $this->object = $object;

        if ($compute) {
            $this->snapshot = (array) $this->object;
        }
    }

    public function isDirty()
    {
        if (isset($this->snapshot)) {
            return $this->snapshot !== (array) $this->object;
        }

        // No need to compute - we don't have a snapshot
        return false;
    }

    public function takeSnapshot()
    {
        return new static($this->object, true);
    }

    public function getSnapshot()
    {
        return $this->snapshot;
    }

    public function clear()
    {
        return new static($this->object, false);
    }

    public function getObject()
    {
        return $this->object;
    }
}
