<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface ResourceDeletionProcessorInterface
{
    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param mixed                $resource
     */
    public function process(ContextDefinitionInterface $contextDefinition, $resource);

    /**
     * @param ContextDefinitionInterface  $contextDefinition
     * @param ResourceMetaInterface $resourceMeta
     */
    public function processByResourceMeta(ContextDefinitionInterface $contextDefinition, ResourceMetaInterface $resourceMeta);
}
