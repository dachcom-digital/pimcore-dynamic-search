<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Exception\DocumentTransformerNotFoundException;
use DynamicSearchBundle\Resource\ResourceScaffolderContainerInterface;

interface ResourceScaffolderResolverInterface
{
    /**
     * @param string $dataProviderName
     * @param mixed  $resource
     *
     * @return ResourceScaffolderContainerInterface
     *
     * @throws DocumentTransformerNotFoundException
     */
    public function resolve(string $dataProviderName, $resource);
}