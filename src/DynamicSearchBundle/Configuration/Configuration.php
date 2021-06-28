<?php

namespace DynamicSearchBundle\Configuration;

class Configuration implements ConfigurationInterface
{
    protected array $config = [];

    public function setConfig(array $config = []): void
    {
        $this->config = $config;
    }

    public function get(string $slot)
    {
        return $this->config[$slot];
    }
}
