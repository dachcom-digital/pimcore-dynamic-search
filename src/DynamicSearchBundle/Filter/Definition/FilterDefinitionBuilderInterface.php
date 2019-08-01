<?php

namespace DynamicSearchBundle\Filter\Definition;

use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;

interface FilterDefinitionBuilderInterface
{
    /**
     * @param string                          $contextName
     * @param OutputChannelAllocatorInterface $outputChannelAllocator
     *
     * @return bool
     */
    public function isApplicable(string $contextName, OutputChannelAllocatorInterface $outputChannelAllocator);

    /**
     * @param FilterDefinitionInterface $definition
     *
     * @return FilterDefinitionInterface
     */
    public function buildDefinition(FilterDefinitionInterface $definition);
}
