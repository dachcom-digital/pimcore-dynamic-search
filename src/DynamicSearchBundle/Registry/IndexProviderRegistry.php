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
     * @param IndexProviderInterface $service
     * @param string                 $identifier
     */
    public function register($service, string $identifier)
    {
        if (!in_array(IndexProviderInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), IndexProviderInterface::class, implode(', ', class_implements($service)))
            );
        }

        $this->provider[$identifier] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $identifier)
    {
        return isset($this->provider[$identifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $identifier)
    {
        if (!$this->has($identifier)) {
            throw new \Exception('"' . $identifier . '" Index Provider does not exist');
        }

        return $this->provider[$identifier];
    }
}
