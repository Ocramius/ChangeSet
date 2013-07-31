<?php

namespace ChangeSet\ChangeSetListener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventInterface;

class RemovalListener extends AbstractListenerAggregate
{
    public function attach(EventManagerInterface $eventManager)
    {
        $this->listeners[] = $eventManager->attach('registerRemoved', array($this, 'cascadeCollections'), 100);
        $this->listeners[] = $eventManager->attach('registerRemoved', array($this, 'cascadeAssociations'), 50);
    }

    public function cascadeCollections(EventInterface $event)
    {
    }

    public function cascadeAssociations(EventInterface $event)
    {
    }
}
