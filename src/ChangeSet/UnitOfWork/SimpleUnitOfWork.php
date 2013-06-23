<?php

namespace ChangeSet\UnitOfWork;

class SimpleUnitOfWork implements UnitOfWorkInterface
{
	public function __construct()
	{
		
	}
	
	public function registerClean($object)
	{
		
	}
	
	public function registerNew($object)
	{
	}
	
	public function registerDirty($object)
	{
	}
	
	public function registerRemoved($object)
	{
	}
	
	public function commit()
	{
	}
}