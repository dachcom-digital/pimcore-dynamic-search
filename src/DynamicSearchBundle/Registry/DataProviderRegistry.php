<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Registry\Storage\RegistryStorage;

class DataProviderRegistry implements DataProviderRegistryInterface
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
     * @param DataProviderInterface $service
     * @param string                $identifier
     * @param string|null           $alias
     */
    public function register($service, string $identifier, ?string $alias)
    {
        if (!in_array(DataProviderInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), DataProviderInterface::class, implode(', ', class_implements($service)))
            );
        }

        $this->registryStorage->store($service, 'dataProvider', $identifier, $alias);
    }

    /**
     * {@inheritdoc}
     */
    public function has($identifier)
    {
        return $this->registryStorage->has('dataProvider', $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function get($identifier)
    {
        return $this->registryStorage->get('dataProvider', $identifier);
    }
}
