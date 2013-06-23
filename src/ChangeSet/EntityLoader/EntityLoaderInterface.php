<?php

namespace ChangeSet\EntityLoader;

interface EntityLoaderInterface
{
	public function loadEntity($id, $className);
}