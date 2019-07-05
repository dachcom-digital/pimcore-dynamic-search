<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ContextConfigurationException;
use DynamicSearchBundle\Registry\ResourceNormalizerRegistryInterface;

class ResourceNormalizerManager implements ResourceNormalizerManagerInterface
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var ResourceNormalizerRegistryInterface
     */
    protected $resourceNormalizerRegistry;

    /**
     * @param ConfigurationInterface              $configuration
     * @param ResourceNormalizerRegistryInterface $resourceNormalizerRegistry
     */
    public function __construct(
        ConfigurationInterface $configuration,
        ResourceNormalizerRegistryInterface $resourceNormalizerRegistry
    ) {
        $this->configuration = $configuration;
        $this->resourceNormalizerRegistry = $resourceNormalizerRegistry;
    }

    /**
     * {@inheritDoc}
     */
    public function getResourceNormalizer(ContextDataInterface $contextData)
    {
        $normalizerName = $contextData->getResourceNormalizerName();
        $resourceIdBuilderName = $contextData->getResourceIdBuilderName();
        $dataProviderName = $contextData->getDataProviderName();

        if (is_null($dataProviderName)) {
            return null;
        }

        if (!$this->resourceNormalizerRegistry->hasNormalizerForDataProvider($dataProviderName, $normalizerName)) {
            return null;
        }

        $normalizer = $this->resourceNormalizerRegistry->getNormalizerForDataProvider($dataProviderName, $normalizerName);
        $idBuilder = $this->resourceNormalizerRegistry->getIdBuilderForDataProvider($dataProviderName, $resourceIdBuilderName);

        try {
            $dataProviderOptions = $contextData->getResourceOptions($normalizer);
        } catch (ContextConfigurationException $e) {
            return null;
        }

        $idBuilder->setOptions($dataProviderOptions);

        $normalizer->setIdBuilder($idBuilder);
        $normalizer->setOptions($dataProviderOptions);

        return $normalizer;
    }
}
