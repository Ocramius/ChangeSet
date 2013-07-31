<?php

namespace ChangeSet\UnitOfWork;

use ChangeSet\ChangeTracking\ChangeMap;
use ChangeSet\Committer\CommitterInterface;
use ChangeSet\Reverter\ReverterInterface;
use Zend\EventManager\EventManagerInterface;

class SimpleUnitOfWork implements UnitOfWorkInterface
{
    protected $eventManager;
    protected $changeMap;

    public function __construct(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
        $this->changeMap    = new ChangeMap();
    }

    public function registerClean($object)
    {
        if ($change = $this->changeMap->register($object)) {
            $this->eventManager->trigger(
                __FUNCTION__,
                $this,
                ['object' => $object, 'change' => $change]
            );
        }
    }

    public function registerNew($object)
    {
        if ($change = $this->changeMap->add($object)) {
            $this->eventManager->trigger(
                __FUNCTION__,
                $this,
                ['object' => $object, 'change' => $change]
            );
        }
    }

    public function registerRemoved($object)
    {
        if ($change = $this->changeMap->remove($object)) {
            $this->eventManager->trigger(
                __FUNCTION__,
                $this,
                ['object' => $object, 'change' => $change]
            );
        }
    }

    public function commit(CommitterInterface $committer)
    {
        // @todo events here?

        $committer->commit($this->changeMap);

        $this->changeMap = $this->changeMap->clean();
    }

    public function revert(ReverterInterface $reverter)
    {
        $reverter->revert($this->changeMap);

        $this->clear();
    }

    public function clear()
    {
        // @todo events here?

        $this->changeMap = $this->changeMap->clear();
    }
}
