<?php

namespace DynamicSearchBundle\Guard\Validator;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Exception\ContextConfigurationException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Registry\ContextGuardRegistryInterface;
use DynamicSearchBundle\Registry\DataProviderRegistryInterface;

class ResourceValidator implements ResourceValidatorInterface
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
     * @var ContextGuardRegistryInterface
     */
    protected $contextGuardRegistry;

    /**
     * @param ConfigurationInterface        $configuration
     * @param DataProviderRegistryInterface $dataProviderRegistry
     * @param ContextGuardRegistryInterface $contextGuardRegistry
     */
    public function __construct(
        ConfigurationInterface $configuration,
        DataProviderRegistryInterface $dataProviderRegistry,
        ContextGuardRegistryInterface $contextGuardRegistry
    ) {
        $this->configuration = $configuration;
        $this->dataProviderRegistry = $dataProviderRegistry;
        $this->contextGuardRegistry = $contextGuardRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(string $contextName, string $dispatchType, ResourceMetaInterface $resourceMeta, $resource)
    {
        $contextDefinition = $this->configuration->getContextDefinition($dispatchType, $contextName);

        $dataProviderName = $contextDefinition->getDataProviderName();
        $dataProvider = $this->dataProviderRegistry->get($dataProviderName);

        try {
            $dataProviderOptions = $contextDefinition->getDataProviderOptions($dataProvider, DataProviderInterface::PROVIDER_BEHAVIOUR_FULL_DISPATCH);
        } catch (ContextConfigurationException $e) {
            return false;
        }

        foreach ($this->contextGuardRegistry->getAllGuards() as $guard) {
            if ($guard->isValidateDataResource($contextName, $dataProviderName, $dataProviderOptions, $resourceMeta, $resource) === false) {
                return false;
            }
        }

        return true;
    }
}
