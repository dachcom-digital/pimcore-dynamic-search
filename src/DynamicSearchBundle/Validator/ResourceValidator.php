<?php

namespace DynamicSearchBundle\Validator;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Manager\DataManagerInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;

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
     * @param ConfigurationInterface $configuration
     * @param DataManagerInterface   $dataManager
     */
    public function __construct(
        ConfigurationInterface $configuration,
        DataManagerInterface $dataManager
    ) {
        $this->configuration = $configuration;
        $this->dataManager = $dataManager;
    }

    /**
     * {@inheritdoc}
     */
    public function checkUntrustedResourceProxy(string $contextName, string $dispatchType, $resource)
    {
        $contextDefinition = $this->configuration->getContextDefinition($dispatchType, $contextName);
        $dataProvider = $this->getDataProvider($contextName, $dispatchType);

        if (!$dataProvider instanceof DataProviderInterface) {
            return null;
        }

        return $dataProvider->checkUntrustedResourceProxy($contextDefinition, $resource);
    }

    /**
     * {@inheritdoc}
     */
    public function validateUntrustedResource(string $contextName, string $dispatchType, $resource)
    {
        $contextDefinition = $this->configuration->getContextDefinition($dispatchType, $contextName);
        $dataProvider = $this->getDataProvider($contextName, $dispatchType);

        if (!$dataProvider instanceof DataProviderInterface) {
            return false;
        }

        return $dataProvider->validateUntrustedResource($contextDefinition, $resource);
    }

    /**
     * @param string $contextName
     * @param string $dispatchType
     *
     * @return bool|DataProviderInterface
     */
    protected function getDataProvider(string $contextName, string $dispatchType)
    {
        $contextDefinition = $this->configuration->getContextDefinition($dispatchType, $contextName);

        try {
            $dataProvider = $this->dataManager->getDataProvider($contextDefinition, DataProviderInterface::PROVIDER_BEHAVIOUR_FULL_DISPATCH);
        } catch (ProviderException $e) {
            return false;
        }

        return $dataProvider;
    }
}
