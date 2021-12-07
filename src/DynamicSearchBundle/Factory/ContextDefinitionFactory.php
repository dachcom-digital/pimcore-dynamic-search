<?php

namespace DynamicSearchBundle\Factory;

use DynamicSearchBundle\Context\ContextDefinition;
use DynamicSearchBundle\Context\ContextDefinitionInterface;

class ContextDefinitionFactory implements ContextDefinitionFactoryInterface
{
    protected array $contextConfig = [];

    public function addContextConfig(string $contextName, array $contextConfig): void
    {
        $this->contextConfig[$contextName] = $contextConfig;
    }

    public function replaceContextConfig(string $contextName, array $contextConfig): void
    {
        if (!isset($this->contextConfig[$contextName])) {
            return;
        }

        $this->contextConfig[$contextName] = $contextConfig;
    }

    public function createSingle(string $contextName, string $dispatchType, array $runtimeValues = []): ?ContextDefinitionInterface
    {
        if (!isset($this->contextConfig[$contextName])) {
            return null;
        }

        return new ContextDefinition($dispatchType, $contextName, $this->contextConfig[$contextName], $runtimeValues);
    }

    public function createStack(string $dispatchType, array $runtimeValues = []): array
    {
        $contextStack = [];
        foreach ($this->contextConfig as $contextName => $contextConfig) {
            $contextStack[] = new ContextDefinition($dispatchType, $contextName, $this->contextConfig[$contextName], $runtimeValues);
        }

        return $contextStack;
    }
}
