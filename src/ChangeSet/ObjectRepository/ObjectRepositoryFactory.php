<?php

namespace ChangeSet\ObjectRepository;

use ChangeSet\UnitOfWork\UnitOfWorkInterface;
use ChangeSet\EntityLoader\EntityLoaderFactory;
use ChangeSet\IdentityMap\IdentityMapInterface;

class ObjectRepositoryFactory
{
	private $unitOfWork;
	private $entityLoaderFactory;
	private $identityMap;
	public function __construct(
		UnitOfWorkInterface $unitOfWork, 
		EntityLoaderFactory $entityLoaderFactory,
		IdentityMapInterface $identityMap
	) {
		$this->unitOfWork = $unitOfWork;
		$this->entityLoaderFactory = $entityLoaderFactory;
		$this->identityMap = $identityMap;
	}
	
	public function getObjectRepository($className)
	{
		return new SimpleObjectRepository(
			$this->unitOfWork, 
			$this->entityLoaderFactory->getEntityLoader($className),
			$this->identityMap
		);
	}
}