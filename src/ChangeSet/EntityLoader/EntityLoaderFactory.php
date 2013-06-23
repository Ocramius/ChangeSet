<?php

namespace ChangeSet\EntityLoader;

class EntityLoaderFactory
{
	public function getEntityLoader()
	{
		return new SimpleEntityLoader();
	}
}