<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Exception\Resolver\DocumentDefinitionNotFoundException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface DocumentDefinitionResolverInterface
{
    /**
     * @param ResourceMetaInterface $resourceMeta
     *
     * @return DocumentDefinitionBuilderInterface[]
     *
     * @throws DocumentDefinitionNotFoundException
     */
    public function resolve(ResourceMetaInterface $resourceMeta);
}
