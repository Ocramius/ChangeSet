<?php

namespace ChangeSet\UnitOfWork;

use ChangeSet\ChangeSet;

class SimpleUnitOfWork implements UnitOfWorkInterface
{
	protected $changeSet;
	public function __construct(ChangeSet $changeSet)
	{
		$this->changeSet = $changeSet;
	}
	
	public function registerClean($object)
	{
		$this->changeSet->register($object);
	}
	
	public function registerNew($object)
	{
		$this->changeSet->add($object);
	}
	
	public function registerRemoved($object)
	{
		$this->changeSet->remove($object);
	}
	
	public function commit()
	{
	}
}