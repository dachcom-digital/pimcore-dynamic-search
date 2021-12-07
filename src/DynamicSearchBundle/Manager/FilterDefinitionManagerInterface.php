<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Filter\Definition\FilterDefinition;
use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;

interface FilterDefinitionManagerInterface
{
    public function generateFilterDefinition(ContextDefinitionInterface $contextDefinition, OutputChannelAllocatorInterface $outputChannelAllocator): ?FilterDefinition;
}
