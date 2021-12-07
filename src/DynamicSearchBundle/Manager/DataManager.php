<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Registry\DataProviderRegistryInterface;

class DataManager implements DataManagerInterface
{
    protected ConfigurationInterface $configuration;
    protected DataProviderRegistryInterface $dataProviderRegistry;

    protected array $validProviders;

    public function __construct(
        ConfigurationInterface $configuration,
        DataProviderRegistryInterface $dataProviderRegistry
    ) {
        $this->configuration = $configuration;
        $this->dataProviderRegistry = $dataProviderRegistry;
    }

    public function getDataProvider(ContextDefinitionInterface $contextDefinition, string $providerBehaviour): DataProviderInterface
    {
        $dataProviderName = $contextDefinition->getDataProviderName();
        $cacheKey = sprintf('%s_%s', $contextDefinition->getName(), $dataProviderName);

        if (isset($this->validProviders[$cacheKey])) {
            return $this->validProviders[$cacheKey];
        }

        if (is_null($dataProviderName) || !$this->dataProviderRegistry->has($dataProviderName)) {
            throw new ProviderException('Invalid requested data provider', $dataProviderName);
        }

        $dataProvider = $this->dataProviderRegistry->get($dataProviderName);
        $dataProvider->setOptions($contextDefinition->getDataProviderOptions($providerBehaviour));

        $this->validProviders[$cacheKey] = $dataProvider;

        return $dataProvider;
    }
}
