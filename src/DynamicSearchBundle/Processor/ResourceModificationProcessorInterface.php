<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\RuntimeException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface ResourceModificationProcessorInterface
{
    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param mixed                      $resource
     *
     * @throws RuntimeException
     */
    public function process(ContextDefinitionInterface $contextDefinition, $resource);

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param ResourceMetaInterface      $resourceMeta
     * @param mixed                      $resource
     *
     * @throws RuntimeException
     */
    public function processByResourceMeta(ContextDefinitionInterface $contextDefinition, ResourceMetaInterface $resourceMeta, $resource);
}
