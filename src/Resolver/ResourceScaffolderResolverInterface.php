<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Exception\Resolver\ResourceScaffolderNotFoundException;
use DynamicSearchBundle\Resource\ResourceScaffolderContainerInterface;

interface ResourceScaffolderResolverInterface
{
    /**
     * @throws ResourceScaffolderNotFoundException
     */
    public function resolve(string $dataProviderName, mixed $resource): ResourceScaffolderContainerInterface;
}
