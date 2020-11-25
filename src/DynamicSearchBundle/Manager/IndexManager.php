<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Registry\IndexRegistryInterface;
use DynamicSearchBundle\Registry\IndexProviderRegistryInterface;

class IndexManager implements IndexManagerInterface
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var IndexProviderRegistryInterface
     */
    protected $indexProviderRegistry;

    /**
     * @var IndexRegistryInterface
     */
    protected $indexRegistry;

    /**
     * @var array
     */
    protected $validProviders;

    /**
     * @param ConfigurationInterface         $configuration
     * @param IndexProviderRegistryInterface $indexProviderRegistry
     * @param IndexRegistryInterface         $indexRegistry
     */
    public function __construct(
        ConfigurationInterface $configuration,
        IndexProviderRegistryInterface $indexProviderRegistry,
        IndexRegistryInterface $indexRegistry
    ) {
        $this->configuration = $configuration;
        $this->indexProviderRegistry = $indexProviderRegistry;
        $this->indexRegistry = $indexRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexProvider(ContextDefinitionInterface $contextDefinition)
    {
        $indexProviderName = $contextDefinition->getIndexProviderName();
        $cacheKey = sprintf('%s_%s', $contextDefinition->getName(), $indexProviderName);

        if (isset($this->validProviders[$cacheKey])) {
            return $this->validProviders[$cacheKey];
        }

        if (is_null($indexProviderName) || !$this->indexProviderRegistry->has($indexProviderName)) {
            throw new ProviderException('Invalid requested index provider', $indexProviderName);
        }

        $indexProvider = $this->indexProviderRegistry->get($indexProviderName);
        $indexProvider->setOptions($contextDefinition->getIndexProviderOptions());

        $this->validProviders[$cacheKey] = $indexProvider;

        return $indexProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexField(ContextDefinitionInterface $contextDefinition, string $identifier)
    {
        $indexProviderName = $contextDefinition->getIndexProviderName();
        if (!$this->indexRegistry->hasFieldForIndexProvider($indexProviderName, $identifier)) {
            return null;
        }

        return $this->indexRegistry->getFieldForIndexProvider($indexProviderName, $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilter(ContextDefinitionInterface $contextDefinition, string $identifier)
    {
        $indexProviderName = $contextDefinition->getIndexProviderName();
        if (!$this->indexRegistry->hasFilterForIndexProvider($indexProviderName, $identifier)) {
            return null;
        }

        return $this->indexRegistry->getFilterForIndexProvider($indexProviderName, $identifier);
    }
}
