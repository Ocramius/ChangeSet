<?php

namespace ChangeSet\EntityLoader;

use ChangeSet\IdentityMap\IdentityMapInterface;

class EntityLoaderFactory
{
	private $identityMap;
	public function __construct(IdentityMapInterface $identityMap)
	{
		$this->identityMap = $identityMap;
	}
	
	public function getEntityLoader($className)
	{
		return new SimpleEntityLoader($this->identityMap);
	}
}