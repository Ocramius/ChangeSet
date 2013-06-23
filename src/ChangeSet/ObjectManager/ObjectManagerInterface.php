<?php

namespace ChangeSet\ObjectManager;

interface ObjectManagerInterface
{
	public function getRepository($className);
	
	public function flush();
	
	public function clear();
}