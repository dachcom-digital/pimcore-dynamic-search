<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Filter\Definition\FilterDefinition;
use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;

interface FilterDefinitionManagerInterface
{
    /**
     * @param ContextDefinitionInterface      $contextDefinition
     * @param OutputChannelAllocatorInterface $outputChannelAllocator
     *
     * @return FilterDefinition|null
     */
    public function generateFilterDefinition(ContextDefinitionInterface $contextDefinition, OutputChannelAllocatorInterface $outputChannelAllocator);
}
