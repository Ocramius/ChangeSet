<?php

namespace ChangeSet\Snapshot;

/**
 * {@inheritDoc}
 *
 * Simplified implementation of {@see \ChangeSet\Snapshot\SnapshotInterface} that
 * simply relays all comparison operations to the {@see \ChangeSet\Snapshot\SnapshotManagerInterface}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class SimplifiedSnapshot implements SnapshotInterface
{
    /**
     * @var object
     */
    private $object;

    /**
     * @var mixed
     */
    private $state;

    /**
     * Constructor.
     *
     * @param object $object
     * @param mixed $state
     */
    public function __construct($object, $state)
    {
        $this->object = $object;
        $this->state  = $state;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize(array(
            'object' => $this->object,
            'state'  => $this->state,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        $this->object = $data['object'];
        $this->state  = $data['state'];
    }

    /**
     * {@inheritDoc}
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * {@inheritDoc}
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * {@inheritDoc}
     */
    public function compare(SnapshotManagerInterface $snapshotManager)
    {
        return $snapshotManager->compareState($this);
    }

    /**
     * {@inheritDoc}
     */
    public function freeze(SnapshotManagerInterface $snapshotManager)
    {
        return $snapshotManager->getSnapshot($this->object);
    }
}
