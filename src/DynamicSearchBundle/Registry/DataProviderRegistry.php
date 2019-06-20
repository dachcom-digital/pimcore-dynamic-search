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
     * @param $identifier
     */
    public function register($service, $identifier)
    {
        if (!in_array(DataProviderInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), DataProviderInterface::class, implode(', ', class_implements($service)))
            );
        }

        $this->provider[$identifier] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function has($identifier)
    {
        return isset($this->provider[$identifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($identifier)
    {
        if (!$this->has($identifier)) {
            throw new \Exception('"' . $identifier . '" Data Provider does not exist');
        }

        return $this->provider[$identifier];
    }
}