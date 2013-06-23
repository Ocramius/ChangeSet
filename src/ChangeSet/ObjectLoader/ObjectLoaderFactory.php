<?php

namespace ChangeSet\ObjectLoader;

use ChangeSet\IdentityMap\IdentityMapInterface;
use ChangeSet\UnitOfWork\UnitOfWorkInterface;

class ObjectLoaderFactory
{
	private $identityMap;
	private $unitOfWork;
	public function __construct(IdentityMapInterface $identityMap, UnitOfWorkInterface $unitOfWork)
	{
		$this->identityMap = $identityMap;
		$this->unitOfWork = $unitOfWork;
	}
	
	public function getObjectLoader($className)
	{
		return new SimpleObjectLoader($this->identityMap, $this->unitOfWork);
	}
}