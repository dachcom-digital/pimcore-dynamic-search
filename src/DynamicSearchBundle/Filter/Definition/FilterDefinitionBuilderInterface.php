<?php

namespace DynamicSearchBundle\Filter\Definition;

use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;

interface FilterDefinitionBuilderInterface
{
    public function isApplicable(string $contextName, OutputChannelAllocatorInterface $outputChannelAllocator): bool;

    public function buildDefinition(FilterDefinitionInterface $definition): FilterDefinitionInterface;
}
