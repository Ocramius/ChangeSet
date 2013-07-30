<?php

namespace ChangeSet\ObjectRepository;

interface RepositoryFactoryInterface
{

    public function getObjectRepository($className);
}
