<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Exception\Resolver\DefinitionNotFoundException;
use DynamicSearchBundle\Filter\Definition\FilterDefinitionBuilderInterface;
use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;

interface FilterDefinitionResolverInterface
{
    /**
     * @return array<int, FilterDefinitionBuilderInterface>
     * @throws DefinitionNotFoundException
     */
    public function resolve(string $contextName, OutputChannelAllocatorInterface $outputChannelAllocator): array;
}
