<?php

namespace DynamicSearchBundle\Factory;

use DynamicSearchBundle\Context\ContextDefinition;

class ContextDefinitionFactory implements ContextDefinitionFactoryInterface
{
    /**
     * @var array
     */
    protected $contextConfig = [];

    /**
     * @param string $contextName
     * @param array  $contextConfig
     */
    public function addContextConfig(string $contextName, array $contextConfig)
    {
        $this->contextConfig[$contextName] = $contextConfig;
    }

    /**
     * @param string $contextName
     * @param array  $contextConfig
     */
    public function replaceContextConfig(string $contextName, array $contextConfig)
    {
        if (!isset($this->contextConfig[$contextName])) {
            return;
        }

        $this->contextConfig[$contextName] = $contextConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function createSingle(string $contextName, string $dispatchType, array $runtimeValues = [])
    {
        if (!isset($this->contextConfig[$contextName])) {
            return null;
        }

        return new ContextDefinition($dispatchType, $contextName, $this->contextConfig[$contextName], $runtimeValues);
    }

    /**
     * {@inheritdoc}
     */
    public function createStack(string $dispatchType, array $runtimeValues = [])
    {
        $contextStack = [];
        foreach ($this->contextConfig as $contextName => $contextConfig) {
            $contextStack[] = new ContextDefinition($dispatchType, $contextName, $this->contextConfig[$contextName], $runtimeValues);
        }

        return $contextStack;
    }
}
