<?php

namespace DynamicSearchBundle\Builder;

use DynamicSearchBundle\Context\ContextDefinitionInterface;

interface ContextDefinitionBuilderInterface
{
    /**
     * @param string $contextName
     * @param string $dispatchType
     * @param array  $runtimeValues
     *
     * @return ContextDefinitionInterface|null
     */
    public function buildContextDefinition(string $contextName, string $dispatchType, array $runtimeValues = []);

    /**
     * @param string $dispatchType
     * @param array  $runtimeValues
     *
     * @return ContextDefinitionInterface[]
     */
    public function buildContextDefinitionStack(string $dispatchType, array $runtimeValues = []);
}
