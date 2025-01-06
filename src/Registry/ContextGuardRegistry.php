<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Guard\ContextGuardInterface;
use DynamicSearchBundle\Registry\Storage\RegistryStorage;

class ContextGuardRegistry implements ContextGuardRegistryInterface
{
    protected RegistryStorage $registryStorage;

    public function __construct()
    {
        $this->registryStorage = new RegistryStorage();
    }

    public function register(ContextGuardInterface $service): void
    {
        $this->registryStorage->store($service, ContextGuardInterface::class,'contextGuard', get_class($service));
    }

    public function getAllGuards(): array
    {
        return $this->registryStorage->getByNamespace('contextGuard');
    }
}
