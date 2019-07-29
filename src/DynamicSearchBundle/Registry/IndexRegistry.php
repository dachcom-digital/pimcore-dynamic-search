<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Filter\FilterInterface;
use DynamicSearchBundle\Index\IndexFieldInterface;

class IndexRegistry implements IndexRegistryInterface
{
    /**
     * @var array
     */
    protected $fields;

    /**
     * @var array
     */
    protected $filter;

    /**
     * @param IndexFieldInterface $service
     * @param string              $identifier
     * @param string              $indexProviderName
     */
    public function registerField($service, string $identifier, string $indexProviderName)
    {
        if (!in_array(IndexFieldInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), IndexFieldInterface::class, implode(', ', class_implements($service)))
            );
        }

        if (!isset($this->fields[$indexProviderName])) {
            $this->fields[$indexProviderName] = [];
        }

        $this->fields[$indexProviderName][$identifier] = $service;
    }

    /**
     * @param IndexFieldInterface $service
     * @param string              $identifier
     * @param string              $indexProviderName
     */
    public function registerFilter($service, string $identifier, string $indexProviderName)
    {
        if (!in_array(FilterInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), FilterInterface::class, implode(', ', class_implements($service)))
            );
        }

        if (!isset($this->filter[$indexProviderName])) {
            $this->filter[$indexProviderName] = [];
        }

        $this->filter[$indexProviderName][$identifier] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldForIndexProvider(string $indexProviderName, string $identifier)
    {
        return $this->fields[$indexProviderName][$identifier];
    }

    /**
     * {@inheritdoc}
     */
    public function hasFieldForIndexProvider(string $indexProviderName, string $identifier)
    {
        return isset($this->fields[$indexProviderName]) && isset($this->fields[$indexProviderName][$identifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterForIndexProvider(string $indexProviderName, string $identifier)
    {
        return $this->filter[$indexProviderName][$identifier];
    }

    /**
     * {@inheritdoc}
     */
    public function hasFilterForIndexProvider(string $indexProviderName, string $identifier)
    {
        return isset($this->filter[$indexProviderName]) && isset($this->filter[$indexProviderName][$identifier]);
    }
}
