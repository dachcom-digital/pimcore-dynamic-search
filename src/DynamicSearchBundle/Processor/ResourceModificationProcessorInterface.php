<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\RuntimeException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface ResourceModificationProcessorInterface
{
    public function process(ContextDefinitionInterface $contextDefinition, $resource): void;

    public function processByResourceMeta(ContextDefinitionInterface $contextDefinition, ResourceMetaInterface $resourceMeta, $resource): void;
}
