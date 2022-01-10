<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Provider\IndexProviderInterface;
use DynamicSearchBundle\Registry\Storage\RegistryStorage;

class IndexProviderRegistry implements IndexProviderRegistryInterface
{
    protected RegistryStorage $registryStorage;

    public function __construct()
    {
        $this->registryStorage = new RegistryStorage();
    }

    public function register(IndexProviderInterface $service, string $identifier, ?string $alias): void
    {
        $this->registryStorage->store($service, IndexProviderInterface::class, 'indexProvider', $identifier, $alias);
    }

    public function has(string $identifier): bool
    {
        return $this->registryStorage->has('indexProvider', $identifier);
    }

    public function get(string $identifier): ?IndexProviderInterface
    {
        return $this->registryStorage->get('indexProvider', $identifier);
    }

    public function all(): array
    {
        return $this->registryStorage->getByNamespace('indexProvider');
    }
}
