<?php

namespace DynamicSearchBundle\Configuration;

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
}
