<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Exception\Resolver\DefinitionNotFoundException;
use DynamicSearchBundle\Registry\DefinitionBuilderRegistryInterface;

class FilterDefinitionResolver implements FilterDefinitionResolverInterface
{
    /**
     * @var DefinitionBuilderRegistryInterface
     */
    protected $definitionBuilderRegistry;

    /**
     * @param DefinitionBuilderRegistryInterface $definitionBuilderRegistry
     */
    public function __construct(DefinitionBuilderRegistryInterface $definitionBuilderRegistry)
    {
        $this->definitionBuilderRegistry = $definitionBuilderRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(string $contextName, string $outputChannelName)
    {
        $builder = [];
        foreach ($this->definitionBuilderRegistry->getAllFilterDefinitionBuilder() as $filterDefinitionBuilder) {
            if ($filterDefinitionBuilder->isApplicable($contextName, $outputChannelName) === true) {
                $builder[] = $filterDefinitionBuilder;
            }
        }

        if (count($builder) === 0) {
            throw new DefinitionNotFoundException('filter');
        }

        return $builder;
    }
}
