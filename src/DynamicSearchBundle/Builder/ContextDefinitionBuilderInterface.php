<?php

namespace DynamicSearchBundle\Builder;

use DynamicSearchBundle\Context\ContextDefinitionInterface;

interface ContextDefinitionBuilderInterface
{
    public function buildContextDefinition(string $contextName, string $dispatchType, array $runtimeValues = []): ?ContextDefinitionInterface;

    /**
     * @return array<int, ContextDefinitionInterface>
     */
    public function buildContextDefinitionStack(string $dispatchType, array $runtimeValues = []): array;
}
