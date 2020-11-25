<?php

namespace DynamicSearchBundle\Builder;

use DynamicSearchBundle\Factory\ContextDefinitionFactoryInterface;

class ContextDefinitionBuilder implements ContextDefinitionBuilderInterface
{
    /**
     * @var ContextDefinitionFactoryInterface
     */
    protected $contextDefinitionFactory;

    /**
     * @param ContextDefinitionFactoryInterface $contextDefinitionFactory
     */
    public function __construct(ContextDefinitionFactoryInterface $contextDefinitionFactory)
    {
        $this->contextDefinitionFactory = $contextDefinitionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function buildContextDefinition(string $contextName, string $dispatchType, array $runtimeValues = [])
    {
        return $this->contextDefinitionFactory->createSingle($contextName, $dispatchType, $runtimeValues);
    }

    /**
     * {@inheritdoc}
     */
    public function buildContextDefinitionStack(string $dispatchType, array $runtimeValues = [])
    {
        return $this->contextDefinitionFactory->createStack($dispatchType, $runtimeValues);
    }

}
