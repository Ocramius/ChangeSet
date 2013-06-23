<?php

namespace ChangeSetTest;

use ChangeSet\ChangeSet;
use PHPUnit_Framework_TestCase;

class ChangeSetTest extends PHPUnit_Framework_TestCase
{
	protected $eventManager;
	protected $changeSet;
	public function setUp()
	{
		$this->eventManager = $this->getMock('Zend\EventManager\EventManagerInterface');
		$this->changeSet = new ChangeSet($this->eventManager);
	}
    public function testRegistersNewInstances()
    {
        $object = new \stdClass();

        $this->assertEmpty($this->changeSet->getNew());

        $this->assertFalse($this->changeSet->isTracking($object));
        $this->changeSet->add($object);
        $this->assertTrue($this->changeSet->isTracking($object));

        $this->assertSame(array($object), $this->changeSet->getNew());

        $this->changeSet->add($object);
        $this->assertSame(
            array($object),
            $this->changeSet->getNew(),
            'Further "new" registration is ignored'
        );
    }

    public function testRegistersManagedInstances()
    {
        $object = new \stdClass();

        $this->assertEmpty($this->changeSet->getChangedManaged());

        $this->assertFalse($this->changeSet->isTracking($object));
        $this->changeSet->register($object);
        $this->assertTrue($this->changeSet->isTracking($object));

        $this->assertEmpty($this->changeSet->getChangedManaged());

        $object->foo = 'bar';

        $this->assertSame(array($object), $this->changeSet->getChangedManaged());

        $this->changeSet->register($object);
        $this->assertSame(
            array($object),
            $this->changeSet->getChangedManaged(),
            'Further "managed" registration is ignored'
        );
    }

    public function testRegistersRemovedInstances()
    {
        $object = new \stdClass();

        $this->assertEmpty($this->changeSet->getRemoved());

        $this->assertFalse($this->changeSet->isTracking($object));
        $this->changeSet->remove($object);
        $this->assertTrue($this->changeSet->isTracking($object));

        $this->assertSame(array($object), $this->changeSet->getRemoved());

        $this->changeSet->remove($object);
        $this->assertSame(
            array($object),
            $this->changeSet->getRemoved(),
            'Further "remove" registration is ignored'
        );
    }

    public function testClear()
    {
        $new = new \stdClass();
        $managed = new \stdClass();
        $removed = new \stdClass();

        $this->changeSet->add($new);
        $this->changeSet->register($managed);
        $this->changeSet->remove($removed);

        $clearedChangeSet = $this->changeSet->clear();

        $this->assertInstanceOf('ChangeSet\\ChangeSet', $clearedChangeSet);
        $this->assertNotSame($this->changeSet, $clearedChangeSet);

        $this->assertFalse($clearedChangeSet->isTracking($new));
        $this->assertFalse($clearedChangeSet->isTracking($managed));
        $this->assertFalse($clearedChangeSet->isTracking($removed));
    }

    public function testClean()
    {
        $new = new \stdClass();
        $managed = new \stdClass();
        $removed = new \stdClass();

        $this->changeSet->add($new);
        $this->changeSet->register($managed);
        $this->changeSet->remove($removed);

        $cleanedChangeSet = $this->changeSet->clean();

        $this->assertInstanceOf('ChangeSet\\ChangeSet', $cleanedChangeSet);
        $this->assertNotSame($this->changeSet, $cleanedChangeSet);

        $this->assertTrue($cleanedChangeSet->isTracking($new));
        $this->assertTrue($cleanedChangeSet->isTracking($managed));
        $this->assertFalse($cleanedChangeSet->isTracking($removed));
    }
}
