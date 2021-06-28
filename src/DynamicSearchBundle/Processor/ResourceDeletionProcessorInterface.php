<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface ResourceDeletionProcessorInterface
{
    public function process(ContextDefinitionInterface $contextDefinition, $resource): void;

    public function processByResourceMeta(ContextDefinitionInterface $contextDefinition, ResourceMetaInterface $resourceMeta): void;
}
