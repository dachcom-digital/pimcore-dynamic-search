<?php

namespace DynamicSearchBundle\Builder;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Factory\ContextDefinitionFactoryInterface;

class ContextDefinitionBuilder implements ContextDefinitionBuilderInterface
{
    protected ContextDefinitionFactoryInterface $contextDefinitionFactory;

    public function __construct(ContextDefinitionFactoryInterface $contextDefinitionFactory)
    {
        $this->contextDefinitionFactory = $contextDefinitionFactory;
    }

    public function buildContextDefinition(string $contextName, string $dispatchType, array $runtimeValues = []): ?ContextDefinitionInterface
    {
        return $this->contextDefinitionFactory->createSingle($contextName, $dispatchType, $runtimeValues);
    }

    public function buildContextDefinitionStack(string $dispatchType, array $runtimeValues = []): array
    {
        return $this->contextDefinitionFactory->createStack($dispatchType, $runtimeValues);
    }

}
