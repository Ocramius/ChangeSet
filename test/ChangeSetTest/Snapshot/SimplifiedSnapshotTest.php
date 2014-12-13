<?php
/**
 * Created by PhpStorm.
 * User: ocramius
 * Date: 17/11/13
 * Time: 10:57
 */

namespace ChangeSetTest\Snapshot;

use ChangeSet\Snapshot\SimplifiedSnapshot;

/**
 * @covers \ChangeSet\Snapshot\SimplifiedSnapshot
 */
class SimplifiedSnapshotTest extends SnapshotTest
{
    public function getSnapshot()
    {
        return new SimplifiedSnapshot(new \stdClass(), array('foo' => 'bar'));
    }
}
