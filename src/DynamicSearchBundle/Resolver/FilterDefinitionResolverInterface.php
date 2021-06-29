<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Exception\Resolver\DefinitionNotFoundException;
use DynamicSearchBundle\Filter\Definition\FilterDefinitionBuilderInterface;
use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;

interface FilterDefinitionResolverInterface
{
    /**
     * @return FilterDefinitionBuilderInterface[]
     */
    public function resolve(string $contextName, OutputChannelAllocatorInterface $outputChannelAllocator): array;
}
