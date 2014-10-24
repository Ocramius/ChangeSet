<?php

namespace ChangeSetTest\Snapshot;

use ChangeSet\Snapshot\SimplifiedSnapshotManager;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * @covers \ChangeSet\Snapshot\SimplifiedSnapshotManager
 */
class SimplifiedSnapshotManagerTest extends PHPUnit_Framework_TestCase
{
    public function testGetSnapshot()
    {
        $snapshotManager = new SimplifiedSnapshotManager();

        $this->assertInstanceOf(
            'ChangeSet\Snapshot\SnapshotInterface',
            $snapshotManager->getSnapshot(new stdClass())
        );
    }

    public function testCompareStateWithNoDifferences()
    {
        $snapshotManager = new SimplifiedSnapshotManager();

        $snapshot = $this->getMock('ChangeSet\Snapshot\SnapshotInterface');

        $snapshot->expects($this->any())->method('getObject')->will($this->returnValue(new stdClass()));
        $snapshot->expects($this->any())->method('getState')->will($this->returnValue(array()));

        $this->assertNull($snapshotManager->compareState($snapshot));
    }

    public function testCompareStateWithDifferences()
    {
        $snapshotManager = new SimplifiedSnapshotManager();

        $snapshot = $this->getMock('ChangeSet\Snapshot\SnapshotInterface');
        $object = new stdClass();

        $object->foo = 'bar';


        $snapshot->expects($this->any())->method('getObject')->will($this->returnValue(new stdClass()));
        $snapshot->expects($this->any())->method('getState')->will($this->returnValue(array('baz' => 'tab')));

        $diff = $snapshotManager->compareState($snapshot);

        $this->assertInternalType('array', $diff);

        $this->markTestIncomplete('The specification for the diffs is not yet done');
        $this->assertArrayHasKey('foo', $diff);
        $this->assertArrayHasKey('baz', $diff);
    }

    public function testCompareSnapshots()
    {
        $this->markTestIncomplete();
    }

    public function testApplySnapshot()
    {
        $this->markTestIncomplete();
    }
} 