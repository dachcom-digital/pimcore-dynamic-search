<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Resource\FieldTransformerInterface;
use DynamicSearchBundle\Resource\ResourceScaffolderInterface;

interface TransformerRegistryInterface
{
    /**
     * @param string $resourceScaffolderName
     * @param string $identifier
     *
     * @return bool
     */
    public function hasResourceFieldTransformer(string $resourceScaffolderName, string $identifier);

    /**
     * @param string $resourceScaffolderName
     * @param string $identifier
     *
     * @return FieldTransformerInterface
     */
    public function getResourceFieldTransformer(string $resourceScaffolderName, string $identifier);

    /**
     * @param string $dataProviderName
     *
     * @return array|ResourceScaffolderInterface[]
     */
    public function getAllResourceScaffolderForDataProvider(string $dataProviderName);
}