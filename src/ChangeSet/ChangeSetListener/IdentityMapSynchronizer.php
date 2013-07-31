<?php

namespace ChangeSet\ChangeSetListener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventInterface;
use ChangeSet\IdentityMap\IdentityMapInterface;

class IdentityMapSynchronizer extends AbstractListenerAggregate
{
    public function __construct(IdentityMapInterface $identityMap)
    {
        $this->identityMap = $identityMap;
    }

    public function attach(EventManagerInterface $eventManager)
    {
        $this->listeners[] = $eventManager->attach('registerNew', array($this, 'addToIdentityMap'));
        $this->listeners[] = $eventManager->attach('registerClean', array($this, 'addToIdentityMap'));
        $this->listeners[] = $eventManager->attach('registerRemoved', array($this, 'removeFromIdentityMap'));
    }

    public function addToIdentityMap(EventInterface $event)
    {
        $this->identityMap->add($event->getParam('object'));
    }

    public function removeFromIdentityMap(EventInterface $event)
    {
        $this->identityMap->remove($event->getParam('object'));
    }
}
