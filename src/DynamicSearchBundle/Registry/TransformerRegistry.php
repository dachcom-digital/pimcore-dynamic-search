<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Registry\Storage\RegistryStorage;
use DynamicSearchBundle\Resource\ResourceScaffolderInterface;
use DynamicSearchBundle\Resource\FieldTransformerInterface;

class TransformerRegistry implements TransformerRegistryInterface
{
    protected RegistryStorage $registryStorage;

    public function __construct()
    {
        $this->registryStorage = new RegistryStorage();
    }

    public function registerResourceScaffolder(ResourceScaffolderInterface $service, string $identifier, ?string $alias, string $dataProvider): void
    {
        $this->registryStorage->store($service, ResourceScaffolderInterface::class, $dataProvider, $identifier, $alias);
    }

    public function registerResourceFieldTransformer(FieldTransformerInterface $service, string $identifier, ?string $alias, string $resourceScaffolder): void
    {
        $this->registryStorage->store($service, FieldTransformerInterface::class, $resourceScaffolder, $identifier, $alias);
    }

    public function hasResourceFieldTransformer(string $resourceScaffolderName, string $identifier): bool
    {
        return $this->registryStorage->has($resourceScaffolderName, $identifier);
    }

    public function getResourceFieldTransformer(string $resourceScaffolderName, string $identifier): FieldTransformerInterface
    {
        return $this->registryStorage->get($resourceScaffolderName, $identifier);
    }

    public function getAllResourceScaffolderForDataProvider(string $dataProviderName): array
    {
        return $this->registryStorage->getByNamespace($dataProviderName);
    }
}
