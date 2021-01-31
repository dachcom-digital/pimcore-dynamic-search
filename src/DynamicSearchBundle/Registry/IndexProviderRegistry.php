<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Provider\IndexProviderInterface;
use DynamicSearchBundle\Registry\Storage\RegistryStorage;

class IndexProviderRegistry implements IndexProviderRegistryInterface
{
    /**
     * @var RegistryStorage
     */
    protected $registryStorage;

    public function __construct()
    {
        $this->registryStorage = new RegistryStorage();
    }

    /**
     * @param IndexProviderInterface $service
     * @param string                 $identifier
     * @param string|null            $alias
     */
    public function register($service, string $identifier, ?string $alias)
    {
        if (!in_array(IndexProviderInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), IndexProviderInterface::class, implode(', ', class_implements($service)))
            );
        }

        $this->registryStorage->store($service, 'indexProvider', $identifier, $alias);
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $identifier)
    {
        return $this->registryStorage->has('indexProvider', $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $identifier)
    {
        return $this->registryStorage->get('indexProvider', $identifier);
    }
}
