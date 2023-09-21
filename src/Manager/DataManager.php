<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Registry\DataProviderRegistryInterface;

class DataManager implements DataManagerInterface
{
    public function __construct(protected DataProviderRegistryInterface $dataProviderRegistry)
    {
    }

    public function getDataProvider(ContextDefinitionInterface $contextDefinition, string $providerBehaviour): DataProviderInterface
    {
        $dataProviderName = $contextDefinition->getDataProviderName();

        if (is_null($dataProviderName) || !$this->dataProviderRegistry->has($dataProviderName)) {
            throw new ProviderException('Invalid requested data provider', $dataProviderName);
        }

        $dataProvider = $this->dataProviderRegistry->get($dataProviderName);
        $dataProvider->setOptions($contextDefinition->getDataProviderOptions($providerBehaviour));

        return $dataProvider;
    }
}
