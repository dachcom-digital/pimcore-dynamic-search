<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ContextConfigurationException;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
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
    public function getDataManger(ContextDataInterface $contextData)
    {
        $dataProviderToken = $contextData->getDataProvider();

        if (is_null($dataProviderToken) || !$this->dataProviderRegistry->has($dataProviderToken)) {
            throw new ProviderException(sprintf('Invalid requested data provider "%s"', $dataProviderToken));
        }

        $dataProvider = $this->dataProviderRegistry->get($dataProviderToken);

        $this->applyProviderOptions($dataProvider, $contextData);

        $dataProvider->setLogger($this->logger);

        return $dataProvider;
    }

    /**
     * @param DataProviderInterface $dataProvider
     * @param ContextDataInterface  $contextData
     *
     * @throws ProviderException
     */
    protected function applyProviderOptions(DataProviderInterface $dataProvider, ContextDataInterface $contextData)
    {
        try {
            $contextData->assertValidContextProviderOptions($dataProvider, ContextDataInterface::DATA_PROVIDER_OPTIONS);
        } catch (ContextConfigurationException $e) {
            throw new ProviderException(sprintf('Invalid context configuration for data provider "%s". Error was: %s', get_class($dataProvider), $e->getMessage()));
        }
    }
}
