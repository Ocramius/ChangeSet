<?php

namespace ChangeSetTest\ObjectManager;

use PHPUnit_Framework_TestCase;
use ChangeSet\UnitOfWork\SimpleUnitOfWork;
use ChangeSet\ObjectRepository\ObjectRepositoryFactory;
use ChangeSet\ObjectLoader\ObjectLoaderFactory;
use ChangeSet\IdentityMap\IdentityMap;
use ChangeSet\ObjectManager\SimpleObjectManager;
use ChangeSet\ChangeSet;
use Zend\EventManager\EventManager;

class ObjectManagerIntegrationTest extends PHPUnit_Framework_TestCase
{
    protected $changeSetEventManager;
    protected $changeSet;
    protected $identityMap;
    protected $unitOfWork;
    protected $objectLoaderFactory;
    protected $repositoryFactory;
    protected $objectManager;
    public function setUp()
    {
        $this->changeSetEventManager = new EventManager();
        $this->changeSet = new ChangeSet($this->changeSetEventManager);
        $this->identityMap = new IdentityMap();
        $this->unitOfWork = new SimpleUnitOfWork($this->changeSet);
        $this->objectLoaderFactory = new ObjectLoaderFactory($this->identityMap, $this->unitOfWork);
        $this->repositoryFactory = new ObjectRepositoryFactory($this->unitOfWork, $this->objectLoaderFactory, $this->identityMap);
        $this->objectManager = new SimpleObjectManager($this->repositoryFactory);
    }

    public function testRepositoryLoad()
    {
        $listener = $this->getMock('stdClass', array('__invoke'));

        $listener->expects($this->exactly(2))->method('__invoke');
        $this->changeSetEventManager->attach('register', $listener);

        // @todo should repositories be fetched somhow differently? Maybe force per-hand instantiation?
        $repository = $this->objectManager->getRepository('stdClass');

        $this->assertInstanceOf('ChangeSet\\ObjectRepository\\ObjectRepositoryInterface', $repository);

        $object = $repository->get(123);

        $this->assertInstanceOf('stdClass', $object);
        $this->assertSame(123, $object->identity);
        $this->assertInternalType('string', $object->foo);
        $this->assertInternalType('string', $object->bar);

        $this->assertNotSame($object, $repository->get(456), 'Loads separate object for a different identifier');
        $this->assertSame($object, $repository->get(123), 'Uses identity map internally');
    }

    public function testRepositoryAdd()
    {
        $listener = $this->getMock('stdClass', array('__invoke'));

        $listener->expects($this->exactly(2))->method('__invoke');

        $this->changeSetEventManager->attach('add', $listener);

        // @todo should repositories be fetched somhow differently? Maybe force per-hand instantiation?
        $repository = $this->objectManager->getRepository('stdClass');

        $this->assertInstanceOf('ChangeSet\\ObjectRepository\\ObjectRepositoryInterface', $repository);

        $foo = new \stdClass();
        $foo->identity = 123;
        $foo->foo = 'test';
        $foo->bar = 'baz';

        // @todo should this throw exceptions on duplicates?
        $repository->add($foo);

        $this->assertSame($foo, $repository->get(123));

        $bar = new \stdClass();
        $bar->identity = 456;
        $bar->foo = 'test2';
        $bar->bar = 'baz2';

        $repository->add($bar);

        $this->assertSame($bar, $repository->get(456));
    }

    public function testRepositoryRemove()
    {
        $listener = $this->getMock('stdClass', array('__invoke'));

        $listener->expects($this->exactly(2))->method('__invoke');

        $this->changeSetEventManager->attach('remove', $listener);

        // @todo should repositories be fetched somhow differently? Maybe force per-hand instantiation?
        $repository = $this->objectManager->getRepository('stdClass');

        $this->assertInstanceOf('ChangeSet\\ObjectRepository\\ObjectRepositoryInterface', $repository);

        $foo = new \stdClass();
        $foo->identity = 123;
        $bar = new \stdClass();
        $bar->identity = 456;

        // @todo should this throw exceptions on duplicates?
        $repository->add($foo);
        $repository->add($bar);

        $repository->remove($foo);
        $repository->remove($bar);
    }
}
