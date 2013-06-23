<?php

namespace ChangeSet\ObjectRepository;

use ChangeSet\UnitOfWork\UnitOfWorkInterface;
use ChangeSet\EntityLoader\EntityLoaderFactory;

class ObjectRepositoryFactory
{
	private $unitOfWork;
	private $entityLoaderFactory;
	public function __construct(UnitOfWorkInterface $unitOfWork, EntityLoaderFactory $entityLoaderFactory)
	{
		$this->unitOfWork = $unitOfWork;
		$this->entityLoaderFactory = $entityLoaderFactory;
	}
	
	public function getObjectRepository($className)
	{
		return new SimpleObjectRepository(
			$this->unitOfWork, 
			$this->entityLoaderFactory->getEntityLoader($className)
		);
	}
}