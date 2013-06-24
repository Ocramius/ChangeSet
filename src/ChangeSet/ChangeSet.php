<?php

namespace ChangeSet;

use Zend\EventManager\EventManagerInterface;

class ChangeSet
{
    private $newInstances;
    private $managedInstances;
    private $removedInstances;
    private $changeGenerator;
    private $eventManager;

    public function __construct(EventManagerInterface $eventManager)
    {
        $this->newInstances = new \SplObjectStorage();
        $this->managedInstances = new \SplObjectStorage();
        $this->removedInstances = new \SplObjectStorage();
        $this->changeGenerator = new ChangeFactory();
        // @todo a simple map is better (much faster) - using an event manager for now
        $this->eventManager = $eventManager;
    }

    // @todo a map is a data structure, probably shouldn't fire events (fire them in the UoW instead)
    public function add($object)
    {
        if (isset($this->newInstances[$object]) || isset($this->managedInstances[$object])) {
            return;
        }

        $change = $this->changeGenerator->getChange($object);

        $this->eventManager->trigger(
            __FUNCTION__,
            $this,
            array('object' => $object, 'change' => $change)
        );

        unset($this->removedInstances[$object]);
        $this->newInstances[$object] = $change;
    }

    // @todo a map is a data structure, probably shouldn't fire events (fire them in the UoW instead)
    public function register($object)
    {
        if (isset($this->managedInstances[$object])) {
            return;
        }

        $change = $this->changeGenerator->getChange($object)->takeSnapshot();

        $this->eventManager->trigger(
            __FUNCTION__,
            $this,
            array('object' => $object, 'change' => $change)
        );

        unset($this->newInstances[$object], $this->removedInstances[$object]);
        $this->managedInstances[$object] = $change;
    }

    // @todo a map is a data structure, probably shouldn't fire events (fire them in the UoW instead)
    public function remove($object)
    {
        if (isset($this->removedInstances[$object])) {
            return;
        }

        $change = $this->changeGenerator->getChange($object)->takeSnapshot();

        $this->eventManager->trigger(
            __FUNCTION__,
            $this,
            array('object' => $object, 'change' => $change)
        );

        unset($this->newInstances[$object], $this->managedInstances[$object]);
        // @todo if a new instance is found, should we schedule this one for removal or just
        // remove it from newInstances?
        $this->removedInstances[$object] = $change;
    }

    public function isTracking($object)
    {
        return isset($this->managedInstances[$object])
            || isset($this->newInstances[$object])
            || isset($this->removedInstances[$object]); // maybe should not check this?
    }

    public function clean()
    {
        $cleaned = new self($this->eventManager);
        $cleaned->eventManager = $this->eventManager;

        foreach ($this->managedInstances as $object) {
            $cleaned->managedInstances[$object] = $this->managedInstances->offsetGet($object)->takeSnapshot();
        }

        foreach ($this->newInstances as $object) {
            $cleaned->managedInstances[$object] = $this->newInstances->offsetGet($object)->takeSnapshot();
        }

        $this->eventManager->trigger(__FUNCTION__, $cleaned, array('previous' => $this));

        return $cleaned;
    }

    public function clear()
    {
        $cleared = new static($this->eventManager);

        $this->eventManager->trigger(__FUNCTION__, $cleared, array('previous' => $this));

        return $cleared;
    }

    public function getNew()
    {
        $items = array();

        foreach ($this->newInstances as $newInstance) {
            $items[] = $newInstance;
        }

        return $items;
    }

    public function getChangedManaged()
    {
        $items = array();

        foreach ($this->managedInstances as $managedInstance) {
            if ($this->managedInstances->offsetGet($managedInstance)->isDirty()) {
                $items[] = $managedInstance;
            }
        }

        return $items;
    }

    public function getRemoved()
    {
        $items = array();

        foreach ($this->removedInstances as $removedInstance) {
            $items[] = $removedInstance;
        }

        return $items;
    }

    public function takeSnapshot()
    {
        // @todo implement immutable snapshot
    }

    public function rollback()
    {
        // @todo implement rollback
    }
}
