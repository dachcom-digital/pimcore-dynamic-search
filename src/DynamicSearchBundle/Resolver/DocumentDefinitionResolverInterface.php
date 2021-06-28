<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Exception\Resolver\DefinitionNotFoundException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface DocumentDefinitionResolverInterface
{
    /**
     * @return DocumentDefinitionBuilderInterface[]
     */
    public function resolveForContext(string $contextName): array;

    /**
     * @return DocumentDefinitionBuilderInterface[]
     */
    public function resolve(string $contextName, ResourceMetaInterface $resourceMeta): array;
}
