<?php

namespace ChangeSetTest;

use ChangeSet\ChangeFactory;
use PHPUnit_Framework_TestCase;

class ChangeFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testGetChange()
    {
        $object = new \stdClass();
        $changeGenerator = new ChangeFactory();
        $change = $changeGenerator->getChange($object);

        $this->assertInstanceOf('ChangeSet\\Change', $change);
        $this->assertFalse($change->isDirty());
    }
}
