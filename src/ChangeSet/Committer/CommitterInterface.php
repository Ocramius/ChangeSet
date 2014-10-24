<?php

namespace ChangeSet\Committer;

use ChangeSet\ChangeTracking\ChangeMap;

interface CommitterInterface
{
    public function commit(ChangeMap $changeSet);
}
