<?php

namespace ChangeSet\EntityLoader;

use ChangeSet\IdentityMap\IdentityMapInterface;
use ChangeSet\UnitOfWork\UnitOfWorkInterface;

class EntityLoaderFactory
{
	private $identityMap;
	private $unitOfWork;
	public function __construct(IdentityMapInterface $identityMap, UnitOfWorkInterface $unitOfWork)
	{
		$this->identityMap = $identityMap;
		$this->unitOfWork = $unitOfWork;
	}
	
	public function getEntityLoader($className)
	{
		return new SimpleEntityLoader($this->identityMap, $this->unitOfWork);
	}
}