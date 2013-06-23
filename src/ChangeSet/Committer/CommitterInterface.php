<?php

namespace ChangeSet\Committer;

use ChangeSet\ChangeSet;

interface CommitterInterface
{
    public function commit(ChangeSet $changeSet);
}