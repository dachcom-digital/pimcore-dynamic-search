<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Exception\Resolver\ResourceScaffolderNotFoundException;
use DynamicSearchBundle\Resource\ResourceScaffolderContainerInterface;

interface ResourceScaffolderResolverInterface
{
    /**
     * @param string $dataProviderName
     * @param mixed  $resource
     *
     * @return ResourceScaffolderContainerInterface
     *
     * @throws ResourceScaffolderNotFoundException
     */
    public function resolve(string $dataProviderName, $resource);
}
