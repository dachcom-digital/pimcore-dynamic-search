<?php

namespace DynamicSearchBundle\State;

use DynamicSearchBundle\Provider\IndexProviderInterface;
use DynamicSearchBundle\Registry\IndexProviderRegistryInterface;

class IndexProviderHealthState implements HealthStateInterface
{
    protected IndexProviderRegistryInterface $indexProviderRegistry;

    public function __construct(IndexProviderRegistryInterface $indexProviderRegistry)
    {
        $this->indexProviderRegistry = $indexProviderRegistry;
    }

    public function getModuleName(): string
    {
        return 'Dynamic Search';
    }

    public function getState(): int
    {
        $indexProviders = $this->indexProviderRegistry->all();

        if (count($indexProviders) === 0) {
            return self::STATE_ERROR;
        }

        return self::STATE_OK;
    }

    public function getTitle(): string
    {
        return 'Available Index Provider';
    }

    public function getComment(): string
    {
        $indexProviders = $this->indexProviderRegistry->all();

        if (count($indexProviders) === 0) {
            return 'No index provider available';
        }

        return implode(', ', array_map(static function (IndexProviderInterface $provider) {
            return (new \ReflectionClass($provider))->getShortName();
        }, $indexProviders));
    }

}
