<?php

namespace ChangeSetTest\IdentityMap;


use ChangeSet\Container\IdentityExtractorContainerInterface;
use ChangeSet\IdentityMap\IdentityMap;
use PHPUnit_Framework_TestCase;
use ChangeSet\IdentityExtractor\SimpleIdentityExtractor;

class IdentityMapTest extends PHPUnit_Framework_TestCase
{
    private $extractorFactory;

    public function setUp()
    {
        $this->extractorFactory = $this->getMock('ChangeSet\IdentityExtractor\IdentityExtractorFactoryInterface');
    }

    public function testIdentityMap()
    {
        $object = new \stdClass();
        $object->identity = 'foo';

        $this
            ->extractorFactory
            ->expects($this->any())
            ->method('getExtractor')
            ->with('stdClass')
            ->will($this->returnValue(new SimpleIdentityExtractor()));

        $identityMap = new IdentityMap($this->extractorFactory);

        $identityMap->add($object);
        $this->assertSame($object, $identityMap->get('stdClass', 'foo'));
        $identityMap->remove($object);
        $this->assertNull($identityMap->get('stdClass', 'foo'));
    }
}
