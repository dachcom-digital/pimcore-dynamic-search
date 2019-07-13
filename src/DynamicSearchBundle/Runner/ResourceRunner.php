<?php

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Exception\RuntimeException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\DataManagerInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Processor\ResourceDeletionProcessorInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;

class ResourceRunner extends AbstractRunner implements ResourceRunnerInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var DataManagerInterface
     */
    protected $dataManager;

    /**
     * @var ResourceDeletionProcessorInterface
     */
    protected $resourceDeletionProcessor;

    /**
     * @param LoggerInterface                    $logger
     * @param ConfigurationInterface             $configuration
     * @param DataManagerInterface               $dataManager
     * @param ResourceDeletionProcessorInterface $resourceDeletionProcessor
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        DataManagerInterface $dataManager,
        ResourceDeletionProcessorInterface $resourceDeletionProcessor
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->dataManager = $dataManager;
        $this->resourceDeletionProcessor = $resourceDeletionProcessor;
    }

    /**
     * {@inheritDoc}
     */
    public function runInsert(string $contextName, ResourceMetaInterface $resourceMeta)
    {
        $contextDefinition = $this->configuration->getContextDefinition(ContextDataInterface::CONTEXT_DISPATCH_TYPE_INSERT, $contextName);

        if (!$contextDefinition instanceof ContextDataInterface) {
            throw new RuntimeException(sprintf('Context configuration "%s" does not exist', $contextName));
        }

        $predefinedOptions = $resourceMeta->getResourceOptions();

        try {
            $dataProvider = $this->dataManager->getDataProvider($contextDefinition, DataProviderInterface::PROVIDER_BEHAVIOUR_SINGLE_DISPATCH, $predefinedOptions);
        } catch (ProviderException $e) {
            $this->logger->error(sprintf('Error while fetching data provider. Error was: %s.', $e->getMessage()), 'resource_runner', $contextDefinition->getName());
            return;
        }

        $dataProvider->provideSingle($contextDefinition, $resourceMeta);
    }

    /**
     * {@inheritDoc}
     */
    public function runUpdate(string $contextName, ResourceMetaInterface $resourceMeta)
    {
        $contextDefinition = $this->configuration->getContextDefinition(ContextDataInterface::CONTEXT_DISPATCH_TYPE_UPDATE, $contextName);

        if (!$contextDefinition instanceof ContextDataInterface) {
            throw new RuntimeException(sprintf('Context configuration "%s" does not exist', $contextName));
        }

        try {
            $dataProvider = $this->dataManager->getDataProvider($contextDefinition, DataProviderInterface::PROVIDER_BEHAVIOUR_SINGLE_DISPATCH, $resourceMeta->getResourceOptions());
        } catch (ProviderException $e) {
            $this->logger->error(sprintf('Error while fetching data provider. Error was: %s.', $e->getMessage()), 'resource_runner', $contextDefinition->getName());
            return;
        }

        $dataProvider->provideSingle($contextDefinition, $resourceMeta);
    }

    /**
     * {@inheritDoc}
     */
    public function runDelete(string $contextName, ResourceMetaInterface $resourceMeta)
    {
        $contextDefinition = $this->configuration->getContextDefinition(ContextDataInterface::CONTEXT_DISPATCH_TYPE_DELETE, $contextName);

        if (!$contextDefinition instanceof ContextDataInterface) {
            throw new RuntimeException(sprintf('Context configuration "%s" does not exist', $contextName));
        }

        $this->resourceDeletionProcessor->processByResourceMeta($contextDefinition, $resourceMeta);
    }

}