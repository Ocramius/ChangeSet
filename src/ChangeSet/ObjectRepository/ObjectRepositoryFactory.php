<?php

namespace ChangeSet\ObjectRepository;

use ChangeSet\UnitOfWork\UnitOfWorkInterface;
use ChangeSet\ObjectLoader\ObjectLoaderFactory;
use ChangeSet\IdentityMap\IdentityMapInterface;

class ObjectRepositoryFactory
{
	private $unitOfWork;
	private $objectLoaderFactory;
	private $identityMap;
	public function __construct(
		UnitOfWorkInterface $unitOfWork, 
		ObjectLoaderFactory $objectLoaderFactory,
		IdentityMapInterface $identityMap
	) {
		$this->unitOfWork = $unitOfWork;
		$this->objectLoaderFactory = $objectLoaderFactory;
		$this->identityMap = $identityMap;
	}
	
	public function getObjectRepository($className)
	{
		return new SimpleObjectRepository(
			$this->unitOfWork, 
			$this->objectLoaderFactory->getObjectLoader($className),
			$this->identityMap
		);
	}
}