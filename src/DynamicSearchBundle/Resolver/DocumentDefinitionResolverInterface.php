<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Exception\Resolver\DefinitionNotFoundException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface DocumentDefinitionResolverInterface
{
    /**
     * @return array<int, DocumentDefinitionBuilderInterface>
     * @throws DefinitionNotFoundException
     */
    public function resolveForContext(string $contextName): array;

    /**
     * @return array<int, DocumentDefinitionBuilderInterface>
     * @throws DefinitionNotFoundException
     */
    public function resolve(string $contextName, ResourceMetaInterface $resourceMeta): array;
}
