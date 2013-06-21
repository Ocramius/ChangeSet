<?php

namespace ChangeSetTest;

use ChangeSet\ChangeGenerator;
use PHPUnit_Framework_TestCase;

class ChangeGeneratorTest extends PHPUnit_Framework_TestCase
{
	public function testGetChange()
	{
		$object = new \stdClass();
		$changeGenerator = new ChangeGenerator();
		$change = $changeGenerator->getChange($object);
		
		$this->assertInstanceOf('ChangeSet\\Change', $change);
		$this->assertFalse($change->isDirty());
	}
}