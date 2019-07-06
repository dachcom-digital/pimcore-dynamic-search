<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Normalizer\ResourceNormalizerInterface;

class ResourceNormalizerRegistry implements ResourceNormalizerRegistryInterface
{
    /**
     * @var array
     */
    protected $normalizer;

    /**
     * @var array
     */
    protected $idBuilder;

    /**
     * @param        $service
     * @param string $identifier
     * @param string $dataProviderName
     */
    public function registerNormalizer($service, string $identifier, string $dataProviderName)
    {
        if (!in_array(ResourceNormalizerInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), ResourceNormalizerInterface::class, implode(', ', class_implements($service)))
            );
        }

        if (!isset($this->normalizer[$dataProviderName])) {
            $this->normalizer[$dataProviderName] = [];
        }

        $this->normalizer[$dataProviderName][$identifier] = $service;
    }

    /**
     * {@inheritDoc}
     */
    public function getNormalizerForDataProvider(string $dataProviderName, string $identifier)
    {
        return $this->normalizer[$dataProviderName][$identifier];
    }

    /**
     * {@inheritDoc}
     */
    public function hasNormalizerForDataProvider(string $dataProviderName, string $identifier)
    {
        return isset($this->normalizer[$dataProviderName]) && isset($this->normalizer[$dataProviderName][$identifier]);
    }
}