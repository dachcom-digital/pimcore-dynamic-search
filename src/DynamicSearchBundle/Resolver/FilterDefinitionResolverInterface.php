<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Exception\Resolver\DefinitionNotFoundException;
use DynamicSearchBundle\Filter\Definition\FilterDefinitionBuilderInterface;
use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;

interface FilterDefinitionResolverInterface
{
    /**
     * @param string                          $contextName
     * @param OutputChannelAllocatorInterface $outputChannelAllocator
     *
     * @return FilterDefinitionBuilderInterface[]
     *
     * @throws DefinitionNotFoundException
     */
    public function resolve(string $contextName, OutputChannelAllocatorInterface $outputChannelAllocator);
}
