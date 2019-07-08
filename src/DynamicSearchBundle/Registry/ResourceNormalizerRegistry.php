<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\Normalizer\ResourceNormalizerInterface;

class ResourceNormalizerRegistry implements ResourceNormalizerRegistryInterface
{
    /**
     * @var array
     */
    protected $normalizer;

    /**
     * @param        $service
     * @param string $identifier
     * @param string $dataProviderName
     */
    public function registerResourceNormalizer($service, string $identifier, string $dataProviderName)
    {
        if (!in_array(ResourceNormalizerInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), ResourceNormalizerInterface::class, implode(', ', class_implements($service)))
            );
        }

        if (!isset($this->normalizer['resource'])) {
            $this->normalizer['resource'] = [];
        }

        if (!isset($this->normalizer['resource'][$dataProviderName])) {
            $this->normalizer['resource'][$dataProviderName] = [];
        }

        $this->normalizer['resource'][$dataProviderName][$identifier] = $service;
    }

    /**
     * @param        $service
     * @param string $identifier
     * @param string $indexProviderName
     */
    public function registerDocumentNormalizer($service, string $identifier, string $indexProviderName)
    {
        if (!in_array(DocumentNormalizerInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), DocumentNormalizerInterface::class, implode(', ', class_implements($service)))
            );
        }

        if (!isset($this->normalizer['document'])) {
            $this->normalizer['document'] = [];
        }

        if (!isset($this->normalizer['document'][$indexProviderName])) {
            $this->normalizer['document'][$indexProviderName] = [];
        }

        $this->normalizer['document'][$indexProviderName][$identifier] = $service;
    }

    /**
     * {@inheritDoc}
     */
    public function getResourceNormalizerForDataProvider(string $dataProviderName, string $identifier)
    {
        return $this->normalizer['resource'][$dataProviderName][$identifier];
    }

    /**
     * {@inheritDoc}
     */
    public function hasResourceNormalizerForDataProvider(string $dataProviderName, string $identifier)
    {
        return isset($this->normalizer['resource'][$dataProviderName]) && isset($this->normalizer['resource'][$dataProviderName][$identifier]);
    }

    /**
     * {@inheritDoc}
     */
    public function getDocumentNormalizerForIndexProvider(string $indexProviderName, string $identifier)
    {
        return $this->normalizer['document'][$indexProviderName][$identifier];
    }

    /**
     * {@inheritDoc}
     */
    public function hasDocumentNormalizerForIndexProvider(string $indexProviderName, string $identifier)
    {
        return isset($this->normalizer['document'][$indexProviderName]) && isset($this->normalizer['document'][$indexProviderName][$identifier]);
    }
}