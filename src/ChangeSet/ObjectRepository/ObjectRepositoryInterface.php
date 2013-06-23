<?php

namespace ChangeSet\ObjectRepository;

interface ObjectRepositoryInterface
{
	public function add($object);
	
	public function remove($object);
	
	public function get($id);
	
	// @todo Selectable interface here? Maybe not...
}
