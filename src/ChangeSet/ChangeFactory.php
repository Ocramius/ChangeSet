<?php

namespace ChangeSet;

class ChangeFactory
{
    public function getChange($object)
    {
        // @todo need a way of getting collections and cascaded items changesets in here, and
        // obviously register them with the changeset automatically - second argument?
        return new Change($object);
    }
}
