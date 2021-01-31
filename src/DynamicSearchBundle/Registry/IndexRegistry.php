<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Filter\FilterInterface;
use DynamicSearchBundle\Index\IndexFieldInterface;
use DynamicSearchBundle\Registry\Storage\RegistryStorage;

class IndexRegistry implements IndexRegistryInterface
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
     * @param IndexFieldInterface $service
     * @param string              $identifier
     * @param string|null         $alias
     * @param string              $indexProviderName
     */
    public function registerField($service, string $identifier, ?string $alias, string $indexProviderName)
    {
        $namespace = sprintf('fields_%s', $indexProviderName);
        $this->registryStorage->store($service, IndexFieldInterface::class, $namespace, $identifier, $alias);
    }

    /**
     * @param IndexFieldInterface $service
     * @param string              $identifier
     * @param string|null         $alias
     * @param string              $indexProviderName
     */
    public function registerFilter($service, string $identifier, ?string $alias, string $indexProviderName)
    {
        $namespace = sprintf('filter_%s', $indexProviderName);
        $this->registryStorage->store($service, FilterInterface::class, $namespace, $identifier, $alias);
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldForIndexProvider(string $indexProviderName, string $identifier)
    {
        $namespace = sprintf('fields_%s', $indexProviderName);

        return $this->registryStorage->get($namespace, $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function hasFieldForIndexProvider(string $indexProviderName, string $identifier)
    {
        $namespace = sprintf('fields_%s', $indexProviderName);

        return $this->registryStorage->has($namespace, $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterForIndexProvider(string $indexProviderName, string $identifier)
    {
        $namespace = sprintf('filter_%s', $indexProviderName);

        return $this->registryStorage->get($namespace, $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function hasFilterForIndexProvider(string $indexProviderName, string $identifier)
    {
        $namespace = sprintf('filter_%s', $indexProviderName);

        return $this->registryStorage->has($namespace, $identifier);
    }
}
