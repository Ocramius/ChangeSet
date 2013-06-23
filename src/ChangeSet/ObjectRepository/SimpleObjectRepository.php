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
			// should instances be replaced or silently ignored? Or should an exception be thrown
			// on un-managed items?
			$this->unitOfWork->registerNew($object);
			
			return true;
		}
		
		return false;
	}
	
	public function remove($object)
	{
		if (! $this->identityMap->contains($object)) {
			// clear any registered items for this identifier (now or later?)
			$object = $managedObject;
		}
		
		$this->unitOfWork->registerRemoved($object);
		
		return true;
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
