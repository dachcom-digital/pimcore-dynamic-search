<?php

namespace DynamicSearchBundle\Factory;

use DynamicSearchBundle\Context\ContextDefinitionInterface;

interface ContextDefinitionFactoryInterface
{
    public function createSingle(string $contextName, string $dispatchType, array $runtimeValues = []): ?ContextDefinitionInterface;

    public function createStack(string $dispatchType, array $runtimeValues = []): array;
}
