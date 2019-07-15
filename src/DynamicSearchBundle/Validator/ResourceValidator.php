<?php

namespace DynamicSearchBundle\Validator;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Manager\DataManagerInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Registry\ContextGuardRegistryInterface;

class ResourceValidator implements ResourceValidatorInterface
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var DataManagerInterface
     */
    protected $dataManager;

    /**
     * @var ContextGuardRegistryInterface
     */
    protected $contextGuardRegistry;

    /**
     * @param ConfigurationInterface        $configuration
     * @param DataManagerInterface          $dataManager
     * @param ContextGuardRegistryInterface $contextGuardRegistry
     */
    public function __construct(
        ConfigurationInterface $configuration,
        DataManagerInterface $dataManager,
        ContextGuardRegistryInterface $contextGuardRegistry
    ) {
        $this->configuration = $configuration;
        $this->dataManager = $dataManager;
        $this->contextGuardRegistry = $contextGuardRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function validateUntrustedResource(string $contextName, string $dispatchType, $resource)
    {
        $contextDefinition = $this->configuration->getContextDefinition($dispatchType, $contextName);

        try {
            $dataProvider = $this->dataManager->getDataProvider($contextDefinition, DataProviderInterface::PROVIDER_BEHAVIOUR_FULL_DISPATCH);
        } catch (ProviderException $e) {
            return false;
        }

        return $dataProvider->validateUntrustedResource($contextDefinition, $resource);

    }
}
