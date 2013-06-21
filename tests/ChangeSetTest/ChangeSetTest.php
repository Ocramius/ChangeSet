<?php

namespace ChangeSetTest;

use ChangeSet\ChangeSet;
use PHPUnit_Framework_TestCase;

class ChangeSetTest extends PHPUnit_Framework_TestCase
{
    public function testRegistersNewInstances()
    {
        $changeSet = new ChangeSet();
        $object = new \stdClass();

        $this->assertEmpty($changeSet->getNew());

        $this->assertFalse($changeSet->isTracking($object));
        $changeSet->add($object);
        $this->assertTrue($changeSet->isTracking($object));

        $this->assertSame(array($object), $changeSet->getNew());

        $changeSet->add($object);
        $this->assertSame(
            array($object),
            $changeSet->getNew(),
            'Further "new" registration is ignored'
        );
    }

    public function testRegistersManagedInstances()
    {
        $changeSet = new ChangeSet();
        $object = new \stdClass();

        $this->assertEmpty($changeSet->getChangedManaged());

        $this->assertFalse($changeSet->isTracking($object));
        $changeSet->register($object);
        $this->assertTrue($changeSet->isTracking($object));

        $this->assertEmpty($changeSet->getChangedManaged());

        $object->foo = 'bar';

        $this->assertSame(array($object), $changeSet->getChangedManaged());

        $changeSet->register($object);
        $this->assertSame(
            array($object),
            $changeSet->getChangedManaged(),
            'Further "managed" registration is ignored'
        );
    }

    public function testRegistersRemovedInstances()
    {
        $changeSet = new ChangeSet();
        $object = new \stdClass();

        $this->assertEmpty($changeSet->getRemoved());

        $this->assertFalse($changeSet->isTracking($object));
        $changeSet->remove($object);
        $this->assertTrue($changeSet->isTracking($object));

        $this->assertSame(array($object), $changeSet->getRemoved());

        $changeSet->remove($object);
        $this->assertSame(
            array($object),
            $changeSet->getRemoved(),
            'Further "remove" registration is ignored'
        );
    }

    public function testClear()
    {
        $changeSet = new ChangeSet();
        $new = new \stdClass();
        $managed = new \stdClass();
        $removed = new \stdClass();

        $changeSet->add($new);
        $changeSet->register($managed);
        $changeSet->remove($removed);

        $clearedChangeSet = $changeSet->clear();

        $this->assertInstanceOf('ChangeSet\\ChangeSet', $clearedChangeSet);
        $this->assertNotSame($changeSet, $clearedChangeSet);

        $this->assertFalse($clearedChangeSet->isTracking($new));
        $this->assertFalse($clearedChangeSet->isTracking($managed));
        $this->assertFalse($clearedChangeSet->isTracking($removed));
    }

    public function testClean()
    {
        $changeSet = new ChangeSet();
        $new = new \stdClass();
        $managed = new \stdClass();
        $removed = new \stdClass();

        $changeSet->add($new);
        $changeSet->register($managed);
        $changeSet->remove($removed);

        $cleanedChangeSet = $changeSet->clean();

        $this->assertInstanceOf('ChangeSet\\ChangeSet', $cleanedChangeSet);
        $this->assertNotSame($changeSet, $cleanedChangeSet);

        $this->assertTrue($cleanedChangeSet->isTracking($new));
        $this->assertTrue($cleanedChangeSet->isTracking($managed));
        $this->assertFalse($cleanedChangeSet->isTracking($removed));
    }
}
