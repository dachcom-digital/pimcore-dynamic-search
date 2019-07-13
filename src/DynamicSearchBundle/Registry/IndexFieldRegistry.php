<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Index\IndexFieldInterface;

class IndexFieldRegistry implements IndexFieldRegistryInterface
{
    /**
     * @var array
     */
    protected $fields;

    /**
     * @param IndexFieldInterface $service
     * @param string              $identifier
     * @param string              $indexProviderName
     */
    public function register($service, string $identifier, string $indexProviderName)
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
     * {@inheritdoc}
     */
    public function getForIndexProvider(string $indexProviderName, string $identifier)
    {
        return $this->fields[$indexProviderName][$identifier];
    }

    /**
     * {@inheritdoc}
     */
    public function hasForIndexProvider(string $indexProviderName, string $identifier)
    {
        return isset($this->fields[$indexProviderName]) && isset($this->fields[$indexProviderName][$identifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexFieldsOfIndexProvider(string $indexProviderName)
    {
        return isset($this->fields[$indexProviderName]);
    }
}
