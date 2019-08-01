<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Filter\Definition\FilterDefinition;
use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;

interface FilterDefinitionManagerInterface
{
    /**
     * @param ContextDataInterface            $contextData
     * @param OutputChannelAllocatorInterface $outputChannelAllocator
     *
     * @return FilterDefinition|null
     */
    public function generateFilterDefinition(ContextDataInterface $contextData, OutputChannelAllocatorInterface $outputChannelAllocator);
}
