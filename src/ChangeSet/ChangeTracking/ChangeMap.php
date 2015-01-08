<?php

namespace ChangeSet\ChangeTracking;

use ChangeSet\Change;
use ChangeSet\ChangeFactory;
use Zend\EventManager\EventManagerInterface;

/**
 * Represents a set of tracked elements that were inserted, modified or removed during a particular
 * transaction
 *
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ChangeMap
{
    /**
     * @var \ChangeSet\Change[] indexed by object hash
     */
    private $newInstances     = [];

    /**
     * @var \ChangeSet\Change[] indexed by object hash
     */
    private $managedInstances = [];

    /**
     * @var \ChangeSet\Change[] indexed by object hash
     */
    private $removedInstances = [];

    /**
     * @var ChangeFactory
     */
    private $changeGenerator;

    public function __construct()
    {
        $this->changeGenerator = new ChangeFactory();
    }

    public function add($object)
    {
        $hash = spl_object_hash($object);

        if (isset($this->newInstances[$hash]) || isset($this->managedInstances[$hash])) {
            return null;
        }

        $change = $this->changeGenerator->getChange($object);

        unset($this->removedInstances[$hash]);

        $this->newInstances[$hash] = $change;

        return $change;
    }

    public function register($object)
    {
        $hash = spl_object_hash($object);

        if (isset($this->managedInstances[$hash])) {
            return null;
        }

        $change = $this->changeGenerator->getChange($object)->takeSnapshot();

        unset($this->newInstances[$hash], $this->removedInstances[$hash]);

        $this->managedInstances[$hash] = $change;

        return $change;
    }

    public function remove($object)
    {
        $hash = spl_object_hash($object);

        if (isset($this->removedInstances[$hash])
            || ! (isset($this->managedInstances[$hash]) || isset($this->newInstances[$hash]))
        ) {
            return null;
        }

        $change = $this->changeGenerator->getChange($object)->takeSnapshot();

        if (isset($this->newInstances[$hash])) {
            unset($this->newInstances[$hash]);

            return $change;
        }

        unset($this->newInstances[$hash], $this->managedInstances[$hash]);

        // @todo if a new instance is found, should we schedule this one for removal or just
        // remove it from newInstances?
        $this->removedInstances[$hash] = $change;

        return $change;
    }

    public function isTracking($object)
    {
        $hash = spl_object_hash($object);

        return isset($this->managedInstances[$hash])
            || isset($this->newInstances[$hash])
            || isset($this->removedInstances[$hash]); // maybe should not check this?
    }

    public function clean()
    {
        $cleaned = new self();

        foreach ($this->managedInstances as $hash => $change) {
            $cleaned->managedInstances[$hash] = $change->takeSnapshot();
        }

        foreach ($this->newInstances as $hash => $change) {
            $cleaned->managedInstances[$hash] = $change->takeSnapshot();
        }

        return $cleaned;
    }

    public function clear()
    {
        $cleared = new static();

        return $cleared;
    }

    /**
     * @return Change[]
     */
    public function getNew()
    {
        return array_values($this->newInstances);
    }

    /**
     * @return Change[]
     */
    public function getChangedManaged()
    {
        return array_filter(
            $this->managedInstances,
            function (Change $change) {
                return $change->isDirty();
            }
        );
    }

    /**
     * @return Change[]
     */
    public function getRemoved()
    {
        return array_values($this->removedInstances);
    }
}
