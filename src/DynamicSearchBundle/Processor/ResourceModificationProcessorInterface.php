<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\RuntimeException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface ResourceModificationProcessorInterface
{
    /**
     * @throws RuntimeException
     */
    public function process(ContextDefinitionInterface $contextDefinition, mixed $resource): void;

    /**
     * @throws RuntimeException
     */
    public function processByResourceMeta(ContextDefinitionInterface $contextDefinition, ResourceMetaInterface $resourceMeta, mixed $resource): void;
}
