<?php

namespace ChangeSetTest\ObjectManager;

use PHPUnit_Framework_TestCase;
use ChangeSet\UnitOfWork\SimpleUnitOfWork;
use ChangeSet\ObjectRepository\ObjectRepositoryFactory;
use ChangeSet\EntityLoader\EntityLoaderFactory;
use ChangeSet\IdentityExtractor\IdentityExtractorFactory;
use ChangeSet\IdentityMap\IdentityMap;
use ChangeSet\ObjectManager\SimpleObjectManager;
use ChangeSet\ChangeSet;
use Zend\EventManager\EventManager;

class ObjectManagerIntegrationTest extends PHPUnit_Framework_TestCase
{
	public function testLoadsObject()
	{
		$eventManager = new EventManager();
		$changeSet = new ChangeSet($eventManager);
		$identityMap = new IdentityMap();
		$unitOfWork = new SimpleUnitOfWork();
		$entityLoaderFactory = new EntityLoaderFactory($identityMap);
		$repositoryFactory = new ObjectRepositoryFactory($unitOfWork, $entityLoaderFactory);
		$objectManager = new SimpleObjectManager($repositoryFactory);
		
		// @todo should repositories be fetched somhow differently?
		$repository = $objectManager->getRepository('stdClass');
		
		$this->assertInstanceOf('ChangeSet\\ObjectRepository\\ObjectRepositoryInterface', $repository);
		
		$object = $repository->get(123);
		
		$this->assertInstanceOf('stdClass', $object);
		$this->assertSame(123, $object->identity);
		$this->assertInternalType('string', $object->foo);
		$this->assertInternalType('string', $object->bar);
		
		$this->assertSame($object, $repository->get(123), 'Uses identity map internally');
		
	}
}
