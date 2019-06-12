<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Provider\DataProviderInterface;

class DataProviderRegistry implements DataProviderRegistryInterface
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
        if (!in_array(DataProviderInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), DataProviderInterface::class, implode(', ', class_implements($service)))
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
            throw new \Exception('"' . $alias . '" Data Provider does not exist');
        }

        return $this->provider[$alias];
    }
}