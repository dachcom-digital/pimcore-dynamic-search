<?php

namespace DynamicSearchBundle\Configuration;

use DynamicSearchBundle\Context\ContextData;
use DynamicSearchBundle\Context\ContextDataInterface;

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
     * @return array|ContextDataInterface
     */
    public function getContextDefinitions()
    {
        $contextDefinitions = [];
        foreach ($this->config['context'] as $contextName => $context) {
            $contextDefinitions[] = $this->getContextDefinition($contextName);
        }

        return $contextDefinitions;
    }

    /**
     * @param string $contextName
     *
     * @return ContextDataInterface
     */
    public function getContextDefinition(string $contextName)
    {
        if (!isset($this->config['context'][$contextName]) || !is_array($this->config['context'][$contextName])) {
            return null;
        }

        return new ContextData($contextName, $this->config['context'][$contextName]);
    }
}
