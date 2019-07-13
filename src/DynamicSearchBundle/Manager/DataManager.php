<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ContextConfigurationException;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Registry\DataProviderRegistryInterface;

class DataManager implements DataManagerInterface
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var DataProviderRegistryInterface
     */
    protected $dataProviderRegistry;

    /**
     * @var array
     */
    protected $validProviders;

    /**
     * @param ConfigurationInterface        $configuration
     * @param DataProviderRegistryInterface $dataProviderRegistry
     */
    public function __construct(
        ConfigurationInterface $configuration,
        DataProviderRegistryInterface $dataProviderRegistry
    ) {
        $this->configuration = $configuration;
        $this->dataProviderRegistry = $dataProviderRegistry;
    }

    /**
     * {@inheritDoc}
     */
    public function getDataProvider(ContextDataInterface $contextData, string $providerBehaviour, array $predefinedOptions = [])
    {
        $dataProviderName = $contextData->getDataProviderName();
        $cacheKey = sprintf('%s_%s', $contextData->getName(), $dataProviderName);

        if (isset($this->validProviders[$cacheKey])) {
            return $this->validProviders[$cacheKey];
        }

        if (is_null($dataProviderName) || !$this->dataProviderRegistry->has($dataProviderName)) {
            throw new ProviderException('Invalid requested data provider', $dataProviderName);
        }

        $dataProvider = $this->dataProviderRegistry->get($dataProviderName);

        try {
            $dataProviderOptions = $contextData->getDataProviderOptions($dataProvider, $providerBehaviour, $predefinedOptions);
        } catch (ContextConfigurationException $e) {
            throw new ProviderException($e->getMessage(), $contextData->getDataProviderName(), $e);
        }

        $dataProvider->setOptions($dataProviderOptions);

        $this->validProviders[$cacheKey] = $dataProvider;

        return $dataProvider;
    }
}
