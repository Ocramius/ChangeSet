<?php

namespace ChangeSet\EntityLoader;

use ChangeSet\IdentityMap\IdentityMapInterface;

class SimpleEntityLoader implements EntityLoaderInterface
{
	private $identityMap;
	public function __construct(IdentityMapInterface $identityMap)
	{
		$this->identityMap = $identityMap;
	}
	
	public function loadEntity($className, $id)
	{
		if ($object = $this->identityMap->get($className, $id)) {
			return $object;
		}
		
		$loaded = $this->doFakeLoading($id);
		
		$this->identityMap->add($loaded);
		
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