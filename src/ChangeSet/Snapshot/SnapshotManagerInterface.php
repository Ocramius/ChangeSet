<?php

namespace ChangeSet\Snapshot;

/**
 * Snapshot manager responsible of creating and comparing snapshots
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 *
 * @todo add @throws annotations!
 */
interface SnapshotManagerInterface
{
    /**
     * Get a snapshot of the given object.
     *
     * @param object $object
     *
     * @return SnapshotInterface
     */
    public function getSnapshot($object);

    /**
     * Compares two given snapshots and produces an array of differences between them
     * if there are any.
     *
     * @param SnapshotInterface $left
     * @param SnapshotInterface $right
     *
     * @return array|null
     */
    public function compareSnapshots(SnapshotInterface $left, SnapshotInterface $right);

    /**
     * Compares a passed in snapshot with the object it refers to and retrieves an
     * array of differences if there are any.
     *
     * @param SnapshotInterface $snapshot
     *
     * @return array|null
     */
    public function compareState(SnapshotInterface $snapshot);

    /**
     * Applies the given snapshot's state to a given object
     *
     * @param object $object (must be compatible with the given state)
     * @param SnapshotInterface $snapshot
     *
     * @return void
     */
    public function applySnapshot($object, SnapshotInterface $snapshot);
}
