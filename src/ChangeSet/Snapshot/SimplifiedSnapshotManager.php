<?php

namespace ChangeSet\Snapshot;

/**
 * {@inheritDoc}
 *
 * Simplified implementation of a snapshot manager - relies
 * on object-to-array conversion for creating diffs,
 * and uses simple strict comparison to compute changesets between
 * those diffs
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class SimplifiedSnapshotManager implements SnapshotManagerInterface
{
    /**
     * {@inheritDoc}
     */
    public function getSnapshot($object)
    {
        return new SimplifiedSnapshot($object, (array) $object);
    }

    /**
     * {@inheritDoc}
     */
    public function compareSnapshots(SnapshotInterface $left, SnapshotInterface $right)
    {
        $leftState  = $left->getState();
        $rightState = $right->getState();

        if ($leftState === $rightState) {
            return null;
        }

        if (! is_array($leftState) || ! is_array($rightState)) {
            throw new \UnexpectedValueException(__CLASS__ . ' can only handle array-serialized state');
        }

        return array_diff($leftState, $rightState);
    }

    /**
     * {@inheritDoc}
     */
    public function compareState(SnapshotInterface $snapshot)
    {
        $state = $snapshot->getState();

        if (! is_array($state)) {
            throw new \UnexpectedValueException(__CLASS__ . ' can only handle array-serialized state');
        }

        $objectState   = (array) $snapshot->getObject();
        $snapshotState = $snapshot->getState();

        if ($objectState === $snapshotState) {
            return null;
        }

        return array_diff($objectState, $snapshotState);
    }

    /**
     * {@inheritDoc}
     */
    public function applySnapshot($object, SnapshotInterface $snapshot)
    {
        throw new \BadMethodCallException('Not yet implemented!');
        // @todo not yet implemented!
    }
}
