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
    protected ConfigurationInterface $configuration;
    protected FilterDefinitionResolverInterface $filterDefinitionResolver;

    public function __construct(
        ConfigurationInterface $configuration,
        FilterDefinitionResolverInterface $filterDefinitionResolver
    ) {
        $this->configuration = $configuration;
        $this->filterDefinitionResolver = $filterDefinitionResolver;
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
