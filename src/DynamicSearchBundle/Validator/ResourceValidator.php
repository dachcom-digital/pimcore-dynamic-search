<?php

namespace DynamicSearchBundle\Validator;

use DynamicSearchBundle\Builder\ContextDefinitionBuilderInterface;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Manager\DataManagerInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;

class ResourceValidator implements ResourceValidatorInterface
{
    /**
     * @var ContextDefinitionBuilderInterface
     */
    protected $contextDefinitionBuilder;

    /**
     * @var DataManagerInterface
     */
    protected $dataManager;

    /**
     * @param ContextDefinitionBuilderInterface $contextDefinitionBuilder
     * @param DataManagerInterface              $dataManager
     */
    public function __construct(
        ContextDefinitionBuilderInterface $contextDefinitionBuilder,
        DataManagerInterface $dataManager
    ) {
        $this->contextDefinitionBuilder = $contextDefinitionBuilder;
        $this->dataManager = $dataManager;
    }

    /**
     * {@inheritdoc}
     */
    public function checkUntrustedResourceProxy(string $contextName, string $dispatchType, $resource)
    {
        $contextDefinition = $this->contextDefinitionBuilder->buildContextDefinition($contextName, $dispatchType);
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
        $contextDefinition = $this->contextDefinitionBuilder->buildContextDefinition($contextName, $dispatchType);
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
        $contextDefinition = $this->contextDefinitionBuilder->buildContextDefinition($contextName, $dispatchType);

        try {
            $dataProvider = $this->dataManager->getDataProvider($contextDefinition, DataProviderInterface::PROVIDER_BEHAVIOUR_FULL_DISPATCH);
        } catch (ProviderException $e) {
            return false;
        }

        return $dataProvider;
    }
}
