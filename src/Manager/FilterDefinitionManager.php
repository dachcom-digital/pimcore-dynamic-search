<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\Resolver\DefinitionNotFoundException;
use DynamicSearchBundle\Filter\Definition\FilterDefinition;
use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;
use DynamicSearchBundle\Resolver\FilterDefinitionResolverInterface;

class FilterDefinitionManager implements FilterDefinitionManagerInterface
{
    public function __construct(
        protected ConfigurationInterface $configuration,
        protected FilterDefinitionResolverInterface $filterDefinitionResolver
    ) {
    }

    public function generateFilterDefinition(ContextDefinitionInterface $contextDefinition, OutputChannelAllocatorInterface $outputChannelAllocator): ?FilterDefinition
    {
        try {
            $filterDefinitionBuilderStack = $this->filterDefinitionResolver->resolve($contextDefinition->getName(), $outputChannelAllocator);
        } catch (DefinitionNotFoundException $e) {
            return null;
        }

        $filterDefinition = new FilterDefinition();

        foreach ($filterDefinitionBuilderStack as $filterDefinitionBuilder) {
            $filterDefinitionBuilder->buildDefinition($filterDefinition);
        }

        return $filterDefinition;
    }
}
