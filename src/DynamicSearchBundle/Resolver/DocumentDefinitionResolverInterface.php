<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Exception\Resolver\DefinitionNotFoundException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface DocumentDefinitionResolverInterface
{
    /**
     * @param string                $contextName
     *
     * @return DocumentDefinitionBuilderInterface[]
     *
     * @throws DefinitionNotFoundException
     */
    public function resolveForContext(string $contextName);

    /**
     * @param string                $contextName
     * @param ResourceMetaInterface $resourceMeta
     *
     * @return DocumentDefinitionBuilderInterface[]
     *
     * @throws DefinitionNotFoundException
     */
    public function resolve(string $contextName, ResourceMetaInterface $resourceMeta);
}
