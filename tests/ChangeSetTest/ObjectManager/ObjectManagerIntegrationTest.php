<?php

namespace ChangeSetTest\ObjectManager;

use ChangeSet\ChangeTracking\ChangeMap;
use PHPUnit_Framework_TestCase;
use Zend\EventManager\EventManager;
use ChangeSet\IdentityExtractor\IdentityExtractorFactory;
use ChangeSet\ObjectRepository\RepositoryFactory;
use ChangeSet\IdentityExtractor\SimpleIdentityExtractor;
use ChangeSet\ObjectLoader\SimpleObjectLoader;
use ChangeSet\ObjectRepository\SimpleObjectRepository;
use ChangeSet\UnitOfWork\SimpleUnitOfWork;
use ChangeSet\IdentityMap\IdentityMap;
use ChangeSet\ChangeSetListener\IdentityMapSynchronizer;
use ChangeSet\ObjectManager\SimpleObjectManager;
use ChangeSet\Committer\SimpleLoggingCommitter;
use ChangeSet\Container\Container;

class ObjectManagerIntegrationTest extends PHPUnit_Framework_TestCase
{
    protected $container;

    public function setUp()
    {
        $container = new Container();
        $container['event_manager'] = $container->share(
            function () {
                return new EventManager();
            }
        );

        $container['changemap'] = $container->share(
            function (Container $container) {
                return new ChangeMap($container->offsetGet('event_manager'));
            }
        );

        $container['identity_extractor'] = $container->share(
            function (Container $container) {
                $factory = new IdentityExtractorFactory();
                
                $factory->offsetSet(
                    'stdClass',
                    $factory->share(
                        function (IdentityExtractorFactory $factory) {
                            return new SimpleIdentityExtractor();
                        }
                    )
                );

                return $factory;
            }
        );

        $container['identity_map'] = $container->share(
            function (Container $container) {
                return new IdentityMap($container['identity_extractor']);
            }
        );

        $container['identity_map_synchronizer'] = $container->share(
            function (Container $container) {
                return new IdentityMapSynchronizer($container['identity_map']);
            }
        );

        $container['event_manager']->attach($container['identity_map_synchronizer']);

        $container['unit_of_work'] = $container->share(
            function (Container $container) {
                return new SimpleUnitOfWork($container['changemap']);
            }
        );

        $container['simple_object_loader'] = $container->share(
            function (Container $container) {
                return new SimpleObjectLoader($container['unit_of_work']);
            }
        );

        $container['repository_factory'] = $container->share(
            function (Container $container) {
                $p = new RepositoryFactory();
                $p['stdClass'] = $p->share(
                    function ($p) use ($container) {
                        return new SimpleObjectRepository(
                            $container['unit_of_work'],
                            $container['simple_object_loader'],
                            $container['identity_map'],
                            'stdClass'
                        );
                    }
                );
                return $p;
            }
        );

        $container['object_manager'] = $container->share(
            function (Container $container) {
                return new SimpleObjectManager($container['repository_factory']);
            }
        );

        $container['committer'] = $container->share(
            function (Container $container) {
                return new SimpleLoggingCommitter();
            }
        );

        $this->container = $container;
    }

    public function testRepositoryLoad()
    {
        $container = $this->container;

        $listener = $this->getMock('stdClass', array('__invoke'));

        $listener->expects($this->exactly(2))->method('__invoke');
        $container["event_manager"]->attach('register', $listener);

        // @todo should repositories be fetched somhow differently? Maybe force per-hand instantiation?
        $repository = $container['object_manager']->getRepository('stdClass');

        $this->assertInstanceOf('ChangeSet\\ObjectRepository\\ObjectRepositoryInterface', $repository);

        $object = $repository->get(123);

        $this->assertInstanceOf('stdClass', $object);
        $this->assertSame(123, $object->identity);
        $this->assertInternalType('string', $object->foo);
        $this->assertInternalType('string', $object->bar);

        $this->assertNotSame($object, $repository->get(456), 'Loads separate object for a different identifier');
        $this->assertSame($object, $repository->get(123), 'Uses identity map internally');
        
        $container["unit_of_work"]->commit($container['committer']);
        
        $this->assertEmpty($container['committer']->operations);
        
        $object->foo = 'changed!';
        
        $container["unit_of_work"]->commit($container['committer']);
        $this->assertCount(1, $container['committer']->operations);
        $this->assertSame('update', $container['committer']->operations[0]['type']);
        $this->assertSame($object, $container['committer']->operations[0]['object']);
    }

    public function testRepositoryAdd()
    {
        $container = $this->container;

        $listener = $this->getMock('stdClass', array('__invoke'));

        $listener->expects($this->exactly(2))->method('__invoke');

        $container["event_manager"]->attach('add', $listener);

        // @todo should repositories be fetched somhow differently? Maybe force per-hand instantiation?
        $repository = $container['object_manager']->getRepository('stdClass');

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
        
        $container["unit_of_work"]->commit($container['committer']);
        $this->assertCount(2, $container['committer']->operations);
        $this->assertSame('insert', $container['committer']->operations[0]['type']);
        $this->assertSame($foo, $container['committer']->operations[0]['object']);
        $this->assertSame('insert', $container['committer']->operations[1]['type']);
        $this->assertSame($bar, $container['committer']->operations[1]['object']);
    }

    public function testRepositoryRemove()
    {
        $container = $this->container;

        $listener = $this->getMock('stdClass', array('__invoke'));

        $listener->expects($this->exactly(2))->method('__invoke');

        $container["event_manager"]->attach('remove', $listener);

        // @todo should repositories be fetched somhow differently? Maybe force per-hand instantiation?
        $repository = $container['object_manager']->getRepository('stdClass');

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
        
        $container["unit_of_work"]->commit($container['committer']);
        $this->assertCount(2, $container['committer']->operations);
        $this->assertSame('delete', $container['committer']->operations[0]['type']);
        $this->assertSame($foo, $container['committer']->operations[0]['object']);
        $this->assertSame('delete', $container['committer']->operations[1]['type']);
        $this->assertSame($bar, $container['committer']->operations[1]['object']);
        // @todo not sure deletes should already happen here...
    }
}
