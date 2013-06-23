<?php

namespace ChangeSet\EntityLoader;

class SimpleEntityLoader implements EntityLoaderInterface
{
	private $identityMap;
	public function __construct()
	{
		$this->identityMap = new IdentityMap();
	}
	
	public function load($className, $id)
	{
		$this->identityMap->
	}
}