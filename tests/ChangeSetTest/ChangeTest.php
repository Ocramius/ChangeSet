<?php

namespace ChangeSetTest;

use ChangeSet\Change;
use PHPUnit_Framework_TestCase;

class ChangeTest extends PHPUnit_Framework_TestCase
{
    public function testGetsObject()
    {
        $object = new \stdClass();
        $change = new Change($object);

        $this->assertSame($object, $change->getObject());
    }

    public function testGetsSnapshot()
    {
        $object = new \stdClass();
        $object->foo = 'bar';
        $change = new Change($object);

        $this->assertNull($change->getSnapshot());

        $change = $change->takeSnapshot();

        $this->assertSame(array('foo' => 'bar'), $change->getSnapshot());
    }

    public function testSnapshotDoesNotChange()
    {
        $object = new \stdClass();
        $object->foo = 'bar';
        $change = new Change($object);

        $change = $change->takeSnapshot();

        $this->assertSame(array('foo' => 'bar'), $change->getSnapshot());

        $object->foo = 'baz';
        $this->assertSame(array('foo' => 'bar'), $change->getSnapshot());
        $change = $change->takeSnapshot();
        $this->assertSame(array('foo' => 'baz'), $change->getSnapshot());
        $object->foo = 'tab';
        $this->assertSame(array('foo' => 'baz'), $change->getSnapshot());
    }

    public function testComputesCorrectDirtyState()
    {
        $object = new \stdClass();
        $object->foo = 'bar';
        $change = new Change($object);

        $this->assertFalse($change->isDirty());

        $object->foo = 'baz';

        $this->assertFalse($change->isDirty());

        $change = $change->takeSnapshot();

        $object->foo = 'tab';

        $this->assertTrue($change->isDirty());
    }

    public function testTakingSnapshotProducesNewChange()
    {
        $object = new \stdClass();
        $object->foo = 'bar';
        $change = new Change($object);

        $snapshot = $change->takeSnapshot();

        $this->assertInstanceOf('ChangeSet\\Change', $snapshot);
        $this->assertNotSame($change, $snapshot);
        $this->assertSame($change->getObject(), $snapshot->getObject());
    }
}
