<?php

namespace DynamicSearchBundle\State;

use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Registry\DataProviderRegistryInterface;

class DataProviderHealthState implements HealthStateInterface
{
    public function __construct(protected DataProviderRegistryInterface $dataProviderRegistry)
    {
    }

    public function getModuleName(): string
    {
        return 'Dynamic Search';
    }

    public function getState(): int
    {
        $dataProviders = $this->dataProviderRegistry->all();

        if (count($dataProviders) === 0) {
            return self::STATE_ERROR;
        }

        return self::STATE_OK;
    }

    public function getTitle(): string
    {
        return 'Available Data Provider';
    }

    public function getComment(): string
    {
        $dataProviders = $this->dataProviderRegistry->all();

        if (count($dataProviders) === 0) {
            return 'No data provider available';
        }

        return implode(', ', array_map(static function (DataProviderInterface $provider) {
            return (new \ReflectionClass($provider))->getShortName();
        }, $dataProviders));
    }

}
