<?php

namespace ChangeSet\Reverter;

use ChangeSet\ChangeTracking\ChangeMap;

interface ReverterInterface
{
    public function revert(ChangeMap $changeMap);
}
