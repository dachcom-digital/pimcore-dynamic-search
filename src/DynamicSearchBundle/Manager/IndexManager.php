<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ContextConfigurationException;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;
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
     * @param LoggerInterface                $logger
     * @param ConfigurationInterface         $configuration
     * @param IndexProviderRegistryInterface $indexProviderRegistry
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        IndexProviderRegistryInterface $indexProviderRegistry
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->indexProviderRegistry = $indexProviderRegistry;
    }

    /**
     * {@inheritDoc}
     */
    public function getIndexProvider(ContextDataInterface $contextData)
    {
        $indexProviderToken = $contextData->getIndexProvider();

        if (is_null($indexProviderToken) || !$this->indexProviderRegistry->has($indexProviderToken)) {
            throw new ProviderException('Invalid requested index provider', $indexProviderToken);
        }

        $indexProvider = $this->indexProviderRegistry->get($indexProviderToken);

        $this->applyProviderOptions($indexProvider, $contextData);

        $indexProvider->setLogger($this->logger);

        return $indexProvider;

    }

    /**
     * @param IndexProviderInterface $indexProvider
     * @param ContextDataInterface   $contextData
     *
     * @throws ProviderException
     */
    protected function applyProviderOptions(IndexProviderInterface $indexProvider, ContextDataInterface $contextData)
    {
        try {
            $contextData->assertValidContextProviderOptions($indexProvider, ContextDataInterface::INDEX_PROVIDER_OPTIONS);
        } catch (ContextConfigurationException $e) {
            throw new ProviderException($e->getMessage(), $contextData->getIndexProvider(), $e);
        }
    }
}
