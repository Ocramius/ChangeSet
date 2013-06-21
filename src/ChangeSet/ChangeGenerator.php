<?php

namespace ChangeSet;

class ChangeGenerator
{
	public function getChange($object)
	{
		return new Change($object);
	}
}