<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Registry\Storage\RegistryStorage;
use DynamicSearchBundle\State\HealthStateInterface;

class HealthStateRegistry implements HealthStateRegistryInterface
{
    protected RegistryStorage $registryStorage;

    public function __construct()
    {
        $this->registryStorage = new RegistryStorage();
    }

    public function register(HealthStateInterface $service): void
    {
        $this->registryStorage->store($service, HealthStateInterface::class,'healthState', get_class($service));
    }

    public function all(): array
    {
        return $this->registryStorage->getByNamespace('healthState');
    }
}
