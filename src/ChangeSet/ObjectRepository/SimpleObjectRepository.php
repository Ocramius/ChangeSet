<?php

namespace ChangeSet\ObjectRepository;

use ChangeSet\UnitOfWork\UnitOfWorkInterface;
use ChangeSet\EntityLoader\EntityLoaderInterface;
use ChangeSet\IdentityMap\IdentityMapInterface;

class SimpleObjectRepository implements ObjectRepositoryInterface
{
	protected $unitOfWork;
	protected $entityLoader;
	protected $identityMap;
	public function __construct(
		UnitOfWorkInterface $unitOfWork, 
		EntityLoaderInterface $entityLoader,
		IdentityMapInterface $identityMap
	) {
		$this->unitOfWork = $unitOfWork;
		$this->entityLoader = $entityLoader;
		$this->identityMap = $identityMap;
	}
	
	public function add($object)
	{
		if ($this->identityMap->add($object)) {
			$this->unitOfWork->registerNew($object);
			
			return true;
		}
		
		return false;
	}
	
	public function remove($object)
	{
	}
	
	public function get($id)
	{
		if ($object = $this->identityMap->get('stdClass', $id)) {
			return $object;
		}
		
		$object = $this->entityLoader->loadEntity('stdClass', $id);
		
		$this->identityMap->add($object);
		
		return $object;
	}
}
