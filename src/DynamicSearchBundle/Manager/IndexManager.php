<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ContextConfigurationException;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Registry\IndexFieldRegistryInterface;
use DynamicSearchBundle\Registry\IndexProviderRegistryInterface;

class IndexManager implements IndexManagerInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var IndexProviderRegistryInterface
     */
    protected $indexProviderRegistry;

    /**
     * @var IndexFieldRegistryInterface
     */
    protected $indexFieldRegistry;

    /**
     * @var array
     */
    protected $validProviders;

    /**
     * @param LoggerInterface                $logger
     * @param ConfigurationInterface         $configuration
     * @param IndexProviderRegistryInterface $indexProviderRegistry
     * @param IndexFieldRegistryInterface    $indexFieldRegistry
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        IndexProviderRegistryInterface $indexProviderRegistry,
        IndexFieldRegistryInterface $indexFieldRegistry
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->indexProviderRegistry = $indexProviderRegistry;
        $this->indexFieldRegistry = $indexFieldRegistry;
    }

    /**
     * {@inheritDoc}
     */
    public function getIndexProvider(ContextDataInterface $contextData)
    {
        $indexProviderName = $contextData->getIndexProviderName();
        $cacheKey = sprintf('%s_%s', $contextData->getName(), $indexProviderName);

        if (isset($this->validProviders[$cacheKey])) {
            return $this->validProviders[$cacheKey];
        }

        if (is_null($indexProviderName) || !$this->indexProviderRegistry->has($indexProviderName)) {
            throw new ProviderException('Invalid requested index provider', $indexProviderName);
        }

        $indexProvider = $this->indexProviderRegistry->get($indexProviderName);

        try {
            $indexProviderOptions = $contextData->getIndexProviderOptions($indexProvider);
        } catch (ContextConfigurationException $e) {
            throw new ProviderException($e->getMessage(), $contextData->getIndexProviderName(), $e);
        }

        $indexProvider->setLogger($this->logger);
        $indexProvider->setOptions($indexProviderOptions);

        $this->validProviders[$cacheKey] = $indexProvider;

        return $indexProvider;

    }

    /**
     * {@inheritDoc}
     */
    public function getIndexField(ContextDataInterface $contextData, string $identifier)
    {
        $indexProviderName = $contextData->getIndexProviderName();
        if (!$this->indexFieldRegistry->hasForIndexProvider($indexProviderName, $identifier)) {
            return null;
        }

        return $this->indexFieldRegistry->getForIndexProvider($indexProviderName, $identifier);
    }

    /**
     * {@inheritDoc}
     */
    public function getIndexFieldsOfIndexProvider(ContextDataInterface $contextData)
    {
        $indexProviderName = $contextData->getIndexProviderName();
        return $this->indexFieldRegistry->getIndexFieldsOfIndexProvider($indexProviderName);
    }
}
