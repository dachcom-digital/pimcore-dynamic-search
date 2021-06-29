<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Exception\Resolver\DefinitionNotFoundException;
use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;
use DynamicSearchBundle\Registry\DefinitionBuilderRegistryInterface;

class FilterDefinitionResolver implements FilterDefinitionResolverInterface
{
    protected DefinitionBuilderRegistryInterface $definitionBuilderRegistry;

    public function __construct(DefinitionBuilderRegistryInterface $definitionBuilderRegistry)
    {
        $this->definitionBuilderRegistry = $definitionBuilderRegistry;
    }

    public function resolve(string $contextName, OutputChannelAllocatorInterface $outputChannelAllocator): array
    {
        $builder = [];
        foreach ($this->definitionBuilderRegistry->getAllFilterDefinitionBuilder() as $filterDefinitionBuilder) {
            if ($filterDefinitionBuilder->isApplicable($contextName, $outputChannelAllocator) === true) {
                $builder[] = $filterDefinitionBuilder;
            }
        }

        if (count($builder) === 0) {
            throw new DefinitionNotFoundException('filter');
        }

        return $builder;
    }
}
