<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Registry\ResourceNormalizerRegistryInterface;

class NormalizerManager implements NormalizerManagerInterface
{
    /**
     * @var ResourceNormalizerRegistryInterface
     */
    protected $resourceNormalizerRegistry;

    /**
     * @param ResourceNormalizerRegistryInterface $resourceNormalizerRegistry
     */
    public function __construct(ResourceNormalizerRegistryInterface $resourceNormalizerRegistry)
    {
        $this->resourceNormalizerRegistry = $resourceNormalizerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceNormalizer(ContextDefinitionInterface $contextDefinition)
    {
        $normalizerName = $contextDefinition->getResourceNormalizerName();
        $dataProviderName = $contextDefinition->getDataProviderName();

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
        $normalizer->setOptions($contextDefinition->getResourceNormalizerOptions());

        return $normalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentNormalizerForOutputChannel(ContextDefinitionInterface $contextDefinition, string $outputChannelName)
    {
        $normalizerName = $contextDefinition->getOutputChannelNormalizerName($outputChannelName);
        $indexProviderName = $contextDefinition->getIndexProviderName();

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
        $normalizer->setOptions($contextDefinition->getOutputChannelDocumentNormalizerOptions($outputChannelName));

        return $normalizer;
    }
}
