<?php

namespace ChangeSetTest\ObjectManager;

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
use ChangeSet\ChangeSet;
use ChangeSet\Committer\SimpleLoggingCommitter;
use ChangeSet\Container\Container;

class ObjectManagerIntegrationTest extends PHPUnit_Framework_TestCase
{
    protected $container;

    public function setUp()
    {
        $cnt = new Container();
        $cnt['event_manager'] = $cnt->share(function ($c) {
            return new EventManager();
        });

        $cnt['changeset'] = $cnt->share(function ($c) {
            return  new ChangeSet($c['event_manager']);
        });

        $cnt['identity_extractor'] = $cnt->share(function ($c) {
            $p = new IdentityExtractorFactory();
            $p['stdClass'] = $p->share(function ($c) {
                return new SimpleIdentityExtractor();
            });
            return $p;
        });

        $cnt['identity_map'] = $cnt->share(function ($c) {
            return new IdentityMap($c['identity_extractor']);
        });

        $cnt['identity_map_synchronizer'] = $cnt->share(function ($c) {
            return new IdentityMapSynchronizer($c['identity_map']);
        });

        $cnt['event_manager']->attach($cnt['identity_map_synchronizer']);

        $cnt['unit_of_work'] = $cnt->share(function ($c) {
            return new SimpleUnitOfWork($c['changeset']);
        });

        $cnt['simple_object_loader'] = $cnt->share(function ($c) {
            return new SimpleObjectLoader($c['unit_of_work']);
        });

        $cnt['repository_factory'] = $cnt->share(function ($c) {
            $p = new RepositoryFactory();
            $p['stdClass'] = $p->share(function ($p) use ($c) {
                return new SimpleObjectRepository(
                    $c['unit_of_work'],
                    $c['simple_object_loader'],
                    $c['identity_map'],
                    'stdClass'
                );
            });
            return $p;
        });

        $cnt['object_manager'] = $cnt->share(function ($c) {
            return new SimpleObjectManager($c['repository_factory']);
        });

        $cnt['committer'] = $cnt->share(function ($c) {
            return new SimpleLoggingCommitter();
        });

        $this->container = $cnt;
    }

    public function testRepositoryLoad()
    {
        $cnt = $this->container;

        $listener = $this->getMock('stdClass', array('__invoke'));

        $listener->expects($this->exactly(2))->method('__invoke');
        $cnt["event_manager"]->attach('register', $listener);

        // @todo should repositories be fetched somhow differently? Maybe force per-hand instantiation?
        $repository = $cnt['object_manager']->getRepository('stdClass');

        $this->assertInstanceOf('ChangeSet\\ObjectRepository\\ObjectRepositoryInterface', $repository);

        $object = $repository->get(123);

        $this->assertInstanceOf('stdClass', $object);
        $this->assertSame(123, $object->identity);
        $this->assertInternalType('string', $object->foo);
        $this->assertInternalType('string', $object->bar);

        $this->assertNotSame($object, $repository->get(456), 'Loads separate object for a different identifier');
        $this->assertSame($object, $repository->get(123), 'Uses identity map internally');
        
        $cnt["unit_of_work"]->commit($cnt['committer']);
        
        $this->assertEmpty($cnt['committer']->operations);
        
        $object->foo = 'changed!';
        
        $cnt["unit_of_work"]->commit($cnt['committer']);
        $this->assertCount(1, $cnt['committer']->operations);
        $this->assertSame('update', $cnt['committer']->operations[0]['type']);
        $this->assertSame($object, $cnt['committer']->operations[0]['object']);
    }

    public function testRepositoryAdd()
    {
        $cnt = $this->container;

        $listener = $this->getMock('stdClass', array('__invoke'));

        $listener->expects($this->exactly(2))->method('__invoke');

        $cnt["event_manager"]->attach('add', $listener);

        // @todo should repositories be fetched somhow differently? Maybe force per-hand instantiation?
        $repository = $cnt['object_manager']->getRepository('stdClass');

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
        
        $cnt["unit_of_work"]->commit($cnt['committer']);
        $this->assertCount(2, $cnt['committer']->operations);
        $this->assertSame('insert', $cnt['committer']->operations[0]['type']);
        $this->assertSame($foo, $cnt['committer']->operations[0]['object']);
        $this->assertSame('insert', $cnt['committer']->operations[1]['type']);
        $this->assertSame($bar, $cnt['committer']->operations[1]['object']);
    }

    public function testRepositoryRemove()
    {
        $cnt = $this->container;

        $listener = $this->getMock('stdClass', array('__invoke'));

        $listener->expects($this->exactly(2))->method('__invoke');

        $cnt["event_manager"]->attach('remove', $listener);

        // @todo should repositories be fetched somhow differently? Maybe force per-hand instantiation?
        $repository = $cnt['object_manager']->getRepository('stdClass');

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
        
        $cnt["unit_of_work"]->commit($cnt['committer']);
        $this->assertCount(2, $cnt['committer']->operations);
        $this->assertSame('delete', $cnt['committer']->operations[0]['type']);
        $this->assertSame($foo, $cnt['committer']->operations[0]['object']);
        $this->assertSame('delete', $cnt['committer']->operations[1]['type']);
        $this->assertSame($bar, $cnt['committer']->operations[1]['object']);
        // @todo not sure delets should already happen here...
    }
}
