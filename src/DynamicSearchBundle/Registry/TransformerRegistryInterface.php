<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Resource\FieldTransformerInterface;
use DynamicSearchBundle\Resource\ResourceScaffolderInterface;

interface TransformerRegistryInterface
{
    public function hasResourceFieldTransformer(string $resourceScaffolderName, string $identifier): bool;

    public function getResourceFieldTransformer(string $resourceScaffolderName, string $identifier): FieldTransformerInterface;

    /**
     * @return array<int, ResourceScaffolderInterface>
     */
    public function getAllResourceScaffolderForDataProvider(string $dataProviderName): array;
}
