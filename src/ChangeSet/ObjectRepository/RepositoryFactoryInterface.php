<?php

namespace ChangeSet\ObjectRepository;

Interface RepositoryFactoryInterface
{

    public function getObjectRepository($className);
}