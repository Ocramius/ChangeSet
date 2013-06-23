<?php

namespace ChangeSet\EntityLoader;

class EntityLoader
{
	private $identityMap;
	public function __construct()
	{
		$this->identityMap = new IdentityMap();
	}
	
	public function load($className, $id)
	{
	}
}