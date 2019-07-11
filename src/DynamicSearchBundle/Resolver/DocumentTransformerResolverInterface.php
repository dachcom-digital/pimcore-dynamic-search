<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Exception\DocumentTransformerNotFoundException;
use DynamicSearchBundle\Transformer\DocumentTransformerContainerInterface;

interface DocumentTransformerResolverInterface
{
    /**
     * @param mixed $resource
     *
     * @return DocumentTransformerContainerInterface
     *
     * @throws DocumentTransformerNotFoundException
     */
    public function resolve($resource);
}