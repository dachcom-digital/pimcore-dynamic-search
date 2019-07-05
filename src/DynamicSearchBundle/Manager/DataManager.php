<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ContextConfigurationException;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Registry\DataProviderRegistryInterface;

class DataManager implements DataManagerInterface
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
     * @var DataProviderRegistryInterface
     */
    protected $dataProviderRegistry;

    /**
     * @param LoggerInterface               $logger
     * @param ConfigurationInterface        $configuration
     * @param DataProviderRegistryInterface $dataProviderRegistry
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        DataProviderRegistryInterface $dataProviderRegistry
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->dataProviderRegistry = $dataProviderRegistry;
    }

    /**
     * {@inheritDoc}
     */
    public function getDataProvider(ContextDataInterface $contextData)
    {
        $dataProviderName = $contextData->getDataProviderName();

        if (is_null($dataProviderName) || !$this->dataProviderRegistry->has($dataProviderName)) {
            throw new ProviderException('Invalid requested data provider', $dataProviderName);
        }

        $dataProvider = $this->dataProviderRegistry->get($dataProviderName);

        try {
            $dataProviderOptions = $contextData->getDataProviderOptions($dataProvider);
        } catch (ContextConfigurationException $e) {
            throw new ProviderException($e->getMessage(), $contextData->getDataProviderName(), $e);
        }

        $dataProvider->setLogger($this->logger);
        $dataProvider->setOptions($dataProviderOptions);

        return $dataProvider;
    }
}
