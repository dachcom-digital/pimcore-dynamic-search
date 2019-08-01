<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\Resolver\DefinitionNotFoundException;
use DynamicSearchBundle\Filter\Definition\FilterDefinition;
use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;
use DynamicSearchBundle\Resolver\FilterDefinitionResolverInterface;

class FilterDefinitionManager implements FilterDefinitionManagerInterface
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var FilterDefinitionResolverInterface
     */
    protected $filterDefinitionResolver;

    /**
     * @param ConfigurationInterface            $configuration
     * @param FilterDefinitionResolverInterface $filterDefinitionResolver
     */
    public function __construct(
        ConfigurationInterface $configuration,
        FilterDefinitionResolverInterface $filterDefinitionResolver
    ) {
        $this->configuration = $configuration;
        $this->filterDefinitionResolver = $filterDefinitionResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function generateFilterDefinition(ContextDataInterface $contextData, OutputChannelAllocatorInterface $outputChannelAllocator)
    {
        try {
            $filterDefinitionBuilderStack = $this->filterDefinitionResolver->resolve($contextData->getName(), $outputChannelAllocator);
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
