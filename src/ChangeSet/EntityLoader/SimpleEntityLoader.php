<?php

namespace ChangeSet\EntityLoader;

use ChangeSet\IdentityMap\IdentityMapInterface;
use ChangeSet\UnitOfWork\UnitOfWorkInterface;

class SimpleEntityLoader implements EntityLoaderInterface
{
	private $identityMap;
	private $unitOfWork;
	public function __construct(IdentityMapInterface $identityMap, UnitOfWorkInterface $unitOfWork)
	{
		$this->identityMap = $identityMap;
		$this->unitOfWork = $unitOfWork;
	}
	
	public function loadEntity($className, $id)
	{
		if ($object = $this->identityMap->get($className, $id)) {
			return $object;
		}
		
		$loaded = $this->doFakeLoading($id);
		
		$this->identityMap->add($loaded);
		$this->unitOfWork->registerClean($loaded);
		
		return $loaded;
	}
	
	// @todo handle weak links in here? (proxies) - fetch loaders for other entities?
	// @todo handle collection eager/lazy loading here? etc etc...
	public function doFakeLoading($id)
	{
		$object = new \stdClass();
		
		$object->identity = $id;
		$object->foo = uniqid('foo', true);
		$object->bar = uniqid('bar', true);
		
		return $object;
	}
}