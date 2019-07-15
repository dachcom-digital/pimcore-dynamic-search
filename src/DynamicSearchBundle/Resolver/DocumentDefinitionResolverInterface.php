<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Exception\Resolver\DocumentDefinitionNotFoundException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface DocumentDefinitionResolverInterface
{
    /**
     * @param string                $contextName
     * @param ResourceMetaInterface $resourceMeta
     *
     * @return DocumentDefinitionBuilderInterface[]
     *
     * @throws DocumentDefinitionNotFoundException
     */
    public function resolve(string $contextName, ResourceMetaInterface $resourceMeta);
}
