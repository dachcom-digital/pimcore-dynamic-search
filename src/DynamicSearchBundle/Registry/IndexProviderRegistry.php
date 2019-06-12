<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Provider\IndexProviderInterface;

class IndexProviderRegistry implements IndexProviderRegistryInterface
{
    /**
     * @var array
     */
    protected $provider;

    /**
     * @param $service
     * @param $alias
     */
    public function register($service, $alias)
    {
        if (!in_array(IndexProviderInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), IndexProviderInterface::class, implode(', ', class_implements($service)))
            );
        }

        $this->provider[$alias] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function has($alias)
    {
        return isset($this->provider[$alias]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($alias)
    {
        if (!$this->has($alias)) {
            throw new \Exception('"' . $alias . '" Index Provider does not exist');
        }

        return $this->provider[$alias];
    }
}