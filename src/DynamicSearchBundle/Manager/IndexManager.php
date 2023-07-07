<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Filter\FilterInterface;
use DynamicSearchBundle\Index\IndexFieldInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;
use DynamicSearchBundle\Registry\IndexRegistryInterface;
use DynamicSearchBundle\Registry\IndexProviderRegistryInterface;

class IndexManager implements IndexManagerInterface
{
    public function __construct(
        protected IndexProviderRegistryInterface $indexProviderRegistry,
        protected IndexRegistryInterface $indexRegistry
    ) {
    }

    public function getIndexProvider(ContextDefinitionInterface $contextDefinition): IndexProviderInterface
    {
        $indexProviderName = $contextDefinition->getIndexProviderName();

        if (is_null($indexProviderName) || !$this->indexProviderRegistry->has($indexProviderName)) {
            throw new ProviderException('Invalid requested index provider', $indexProviderName);
        }

        $indexProvider = $this->indexProviderRegistry->get($indexProviderName);
        $indexProvider->setOptions($contextDefinition->getIndexProviderOptions());

        return $indexProvider;
    }

    public function getIndexField(ContextDefinitionInterface $contextDefinition, string $identifier): ?IndexFieldInterface
    {
        $indexProviderName = $contextDefinition->getIndexProviderName();
        if (!$this->indexRegistry->hasFieldForIndexProvider($indexProviderName, $identifier)) {
            return null;
        }

        return $this->indexRegistry->getFieldForIndexProvider($indexProviderName, $identifier);
    }

    public function getFilter(ContextDefinitionInterface $contextDefinition, string $identifier): ?FilterInterface
    {
        $indexProviderName = $contextDefinition->getIndexProviderName();
        if (!$this->indexRegistry->hasFilterForIndexProvider($indexProviderName, $identifier)) {
            return null;
        }

        return $this->indexRegistry->getFilterForIndexProvider($indexProviderName, $identifier);
    }
}
