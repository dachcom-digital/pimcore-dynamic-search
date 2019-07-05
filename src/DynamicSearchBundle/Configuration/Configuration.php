<?php

namespace DynamicSearchBundle\Configuration;

use DynamicSearchBundle\Context\ContextData;

class Configuration implements ConfigurationInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @param array $config
     */
    public function setConfig($config = [])
    {
        $this->config = $config;
    }

    /**
     * @param string $slot
     *
     * @return mixed
     */
    public function get($slot)
    {
        return $this->config[$slot];
    }

    /**
     * {@inheritDoc}
     */
    public function getContextDefinitions(string $dispatchType, array $runtimeValues = [])
    {
        $contextDefinitions = [];
        foreach ($this->config['context'] as $contextName => $context) {
            $contextDefinitions[] = $this->getContextDefinition($dispatchType, $contextName, $runtimeValues);
        }

        return $contextDefinitions;
    }

    /**
     * {@inheritDoc}
     */
    public function getContextDefinition(string $dispatchType, string $contextName, array $runtimeValues = [])
    {
        if (!isset($this->config['context'][$contextName]) || !is_array($this->config['context'][$contextName])) {
            return null;
        }

        return new ContextData($dispatchType, $contextName, $this->config['context'][$contextName], $runtimeValues);
    }
}
