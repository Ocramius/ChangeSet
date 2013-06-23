<?php

namespace ChangeSet\ObjectRepository;

use ChangeSet\UnitOfWork\UnitOfWorkInterface;
use ChangeSet\EntityLoader\EntityLoaderInterface;

class SimpleObjectRepository implements ObjectRepositoryInterface
{
	protected $unitOfWork;
	protected $entityLoader;
	public function __construct(UnitOfWorkInterface $unitOfWork, EntityLoaderInterface $entityLoader)
	{
		$this->unitOfWork = $unitOfWork;
		$this->entityLoader = $entityLoader;
	}
	
	public function add($object)
	{
	}
	
	public function remove($object)
	{
	}
	
	public function get($id)
	{
		return $this->entityLoader->loadEntity('stdClass', $id);
	}
}
