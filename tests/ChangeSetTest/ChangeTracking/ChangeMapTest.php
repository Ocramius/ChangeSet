<?php

namespace ChangeSetTest\ChangeTracking;

use ChangeSet\ChangeTracking\ChangeMap;
use PHPUnit_Framework_TestCase;
use stdClass;

class ChangeMapTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ChangeMap
     */
    protected $changeSet;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->changeSet    = new ChangeMap();
    }

    public function testRegistersNewInstances()
    {
        $object = new stdClass();

        $this->assertEmpty($this->changeSet->getNew());

        $this->assertFalse($this->changeSet->isTracking($object));
        $this->assertInstanceOf('ChangeSet\Change', $this->changeSet->add($object));
        $this->assertTrue($this->changeSet->isTracking($object));

        $new = $this->changeSet->getNew();

        $this->assertCount(1, $new);

        $new = reset($new);

        /* @var $new \ChangeSet\Change */
        $this->assertInstanceOf('ChangeSet\Change', $new);
        $this->assertSame($object, $new->getObject(), 'The object was correctly marked as "new"');
    }

    public function testIgnoresDuplicateNewInstances()
    {
        $object = new stdClass();

        $this->assertInstanceOf('ChangeSet\Change', $this->changeSet->add($object));
        $this->assertNull($this->changeSet->add($object));
        $this->assertCount(1, $this->changeSet->getNew(), 'Further "new" registration is ignored');
    }

    public function testRegistersManagedInstances()
    {
        $object = new stdClass();

        $this->assertEmpty($this->changeSet->getChangedManaged());

        $this->assertFalse($this->changeSet->isTracking($object));
        $this->assertInstanceOf('ChangeSet\Change', $this->changeSet->register($object));
        $this->assertTrue($this->changeSet->isTracking($object));

        $this->assertEmpty($this->changeSet->getChangedManaged());

        $object->foo = 'bar';

        $managed = $this->changeSet->getChangedManaged();

        $this->assertCount(1, $managed);

        $managed = reset($managed);

        /* @var $managed \ChangeSet\Change */
        $this->assertInstanceOf('ChangeSet\Change', $managed);
        $this->assertSame($object, $managed->getObject(), 'The object is being tracked correctly as "changed"');
    }

    public function testIgnoresDuplicateManagedInstances()
    {
        $object = new stdClass();

        $this->assertInstanceOf('ChangeSet\Change', $this->changeSet->register($object));

        $object->foo = 'bar';

        $this->assertNull($this->changeSet->register($object));
        $this->assertCount(
            1,
            $this->changeSet->getChangedManaged(),
            'Further duplicate managed instances are ignored'
        );
    }

    public function testRegistersRemovedInstances()
    {
        $object = new stdClass();

        $this->assertEmpty($this->changeSet->getRemoved());

        $this->assertFalse($this->changeSet->isTracking($object));
        $this->assertInstanceOf('ChangeSet\Change', $this->changeSet->remove($object));
        $this->assertTrue($this->changeSet->isTracking($object));

        $removed = $this->changeSet->getRemoved();

        $this->assertCount(1, $removed);

        $removed = reset($removed);

        /* @var $removed \ChangeSet\Change */
        $this->assertInstanceOf('ChangeSet\Change', $removed);
        $this->assertSame($object, $removed->getObject(), 'The object is being tracked correctly as "removed"');
    }

    public function testIgnoresDuplicateRemovedInstances()
    {
        $object = new stdClass();

        $this->assertInstanceOf('ChangeSet\Change', $this->changeSet->remove($object));
        $this->assertNull($this->changeSet->remove($object));

        $this->assertCount(1, $this->changeSet->getRemoved(), 'Further duplicate removed instances are ignored');
    }

    public function testClear()
    {
        $new     = new stdClass();
        $managed = new stdClass();
        $removed = new stdClass();

        $this->changeSet->add($new);
        $this->changeSet->register($managed);
        $this->changeSet->remove($removed);

        $clearedChangeSet = $this->changeSet->clear();

        $this->assertInstanceOf(get_class($clearedChangeSet), $clearedChangeSet);
        $this->assertNotSame($this->changeSet, $clearedChangeSet);

        $this->assertFalse($clearedChangeSet->isTracking($new));
        $this->assertFalse($clearedChangeSet->isTracking($managed));
        $this->assertFalse($clearedChangeSet->isTracking($removed));
    }

    public function testClean()
    {
        $new     = new stdClass();
        $managed = new stdClass();
        $removed = new stdClass();

        $this->changeSet->add($new);
        $this->changeSet->register($managed);
        $this->changeSet->remove($removed);

        $cleanedChangeSet = $this->changeSet->clean();

        $this->assertInstanceOf(get_class($cleanedChangeSet), $cleanedChangeSet);
        $this->assertNotSame($this->changeSet, $cleanedChangeSet);

        $this->assertTrue($cleanedChangeSet->isTracking($new));
        $this->assertTrue($cleanedChangeSet->isTracking($managed));
        $this->assertFalse($cleanedChangeSet->isTracking($removed), 'Removed instances were completely removed');
    }
}
