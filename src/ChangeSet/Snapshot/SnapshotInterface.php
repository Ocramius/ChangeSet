<?php

namespace ChangeSet\Snapshot;

use Serializable;

/**
 * Snapshot - represents the state of a given object at a particular point in time.
 * Should be immutable.
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
interface SnapshotInterface extends Serializable
{
    /**
     * Retrieve the object this snapshot is connected to
     *
     * @return object
     */
    public function getObject();

    /**
     * Retrieve the snapshot state of the tracked object
     *
     * @return mixed
     */
    public function getState();

    /**
     * Compares the snapshot against the tracked object and produces a new snapshot if there
     * are differences
     *
     * NOTE: while this is a DIP violation (dependency to the snapshot manager), it helps working
     *       around performance issues when the computation is fairly simple, keeping operations
     *       within the snapshot instance where needed
     *
     * @param SnapshotManagerInterface $snapshotManager
     *
     * @return null|Snapshot
     */
    public function compare(SnapshotManagerInterface $snapshotManager);

    /**
     * Produces a new snapshot of the tracked
     *
     * NOTE: while this is a DIP violation (dependency to the snapshot manager), it helps working
     *       around performance issues when the computation is fairly simple, keeping operations
     *       within the snapshot instance where needed
     *
     * @param SnapshotManagerInterface $snapshotManager
     *
     * @return SnapshotInterface
     */
    public function freeze(SnapshotManagerInterface $snapshotManager);
}