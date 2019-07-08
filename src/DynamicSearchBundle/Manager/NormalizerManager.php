<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ContextConfigurationException;
use DynamicSearchBundle\Registry\ResourceNormalizerRegistryInterface;

class NormalizerManager implements NormalizerManagerInterface
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
        $dataProviderName = $contextData->getDataProviderName();

        if (is_null($normalizerName)) {
            return null;
        }

        if (is_null($dataProviderName)) {
            return null;
        }

        if (!$this->resourceNormalizerRegistry->hasResourceNormalizerForDataProvider($dataProviderName, $normalizerName)) {
            return null;
        }

        $normalizer = $this->resourceNormalizerRegistry->getResourceNormalizerForDataProvider($dataProviderName, $normalizerName);

        try {
            $resourceOptions = $contextData->getResourceNormalizerOptions($normalizer);
        } catch (ContextConfigurationException $e) {
            return null;
        }

        $normalizer->setOptions($resourceOptions);

        return $normalizer;
    }

    /**
     * {@inheritDoc}
     */
    public function getDocumentNormalizerForOutputChannel(ContextDataInterface $contextData, string $outputChannelName)
    {
        $normalizerName = $contextData->getOutputChannelNormalizerName($outputChannelName);
        $indexProviderName = $contextData->getIndexProviderName();

        if (is_null($normalizerName)) {
            return null;
        }

        if (is_null($indexProviderName)) {
            return null;
        }

        if (!$this->resourceNormalizerRegistry->hasDocumentNormalizerForIndexProvider($indexProviderName, $normalizerName)) {
            return null;
        }

        $normalizer = $this->resourceNormalizerRegistry->getDocumentNormalizerForIndexProvider($indexProviderName, $normalizerName);

        try {
            $resourceOptions = $contextData->getOutputChannelDocumentNormalizerOptions($normalizer, $outputChannelName);
        } catch (ContextConfigurationException $e) {
            return null;
        }

        $normalizer->setOptions($resourceOptions);

        return $normalizer;
    }
}
