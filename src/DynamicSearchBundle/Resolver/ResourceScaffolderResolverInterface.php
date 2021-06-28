<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Exception\Resolver\ResourceScaffolderNotFoundException;
use DynamicSearchBundle\Resource\ResourceScaffolderContainerInterface;

interface ResourceScaffolderResolverInterface
{
    public function resolve(string $dataProviderName, $resource): ResourceScaffolderContainerInterface;
}
