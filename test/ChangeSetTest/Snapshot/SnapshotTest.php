<?php

namespace ChangeSetTest\Snapshot;

use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * @covers \ChangeSet\Snapshot\SnapshotInterface
 */
class SnapshotTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return \ChangeSet\Snapshot\SnapshotInterface
     */
    public function getSnapshot()
    {
        $snapshot = $this->getMock('ChangeSet\Snapshot\SnapshotInterface');
        $object   = new stdClass();
        $state    = array('foo' => 'bar');

        $snapshot->expects($this->any())->method('serialize')->will($this->returnValue(''));
        $snapshot->expects($this->any())->method('getObject')->will($this->returnValue($object));
        $snapshot->expects($this->any())->method('getState')->will($this->returnValue($state));

        return $snapshot;
    }

    public function testImmutableApi()
    {
        $snapshot = $this->getSnapshot();

        $this->assertSame($snapshot->getObject(), $snapshot->getObject());
        $this->assertSame($snapshot->getState(), $snapshot->getState());
    }

    public function testSerialization()
    {
        $snapshot = $this->getSnapshot();

        if ($snapshot instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->markTestSkipped('Skipped since mocks and serialization don\'t play well together');

        }
        $unserialized = unserialize(serialize($snapshot));

        $this->assertEquals($snapshot->getObject(), $unserialized->getObject());
        $this->assertEquals($snapshot->getState(), $unserialized->getState());
    }
}
