<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Registry\Storage\RegistryStorage;

class DataProviderRegistry implements DataProviderRegistryInterface
{
    protected RegistryStorage $registryStorage;

    public function __construct()
    {
        $this->registryStorage = new RegistryStorage();
    }

    public function register(DataProviderInterface $service, string $identifier, ?string $alias)
    {
        $this->registryStorage->store($service, DataProviderInterface::class, 'dataProvider', $identifier, $alias);
    }

    public function has(string $identifier): bool
    {
        return $this->registryStorage->has('dataProvider', $identifier);
    }

    public function get(string $identifier): DataProviderInterface
    {
        return $this->registryStorage->get('dataProvider', $identifier);
    }
}
