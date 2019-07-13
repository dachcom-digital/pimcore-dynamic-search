<?php

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ProviderException;
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
     * @var DataManagerInterface
     */
    protected $dataManager;

    /**
     * @var ResourceDeletionProcessorInterface
     */
    protected $resourceDeletionProcessor;

    /**
     * @param LoggerInterface                    $logger
     * @param DataManagerInterface               $dataManager
     * @param ResourceDeletionProcessorInterface $resourceDeletionProcessor
     */
    public function __construct(
        LoggerInterface $logger,
        DataManagerInterface $dataManager,
        ResourceDeletionProcessorInterface $resourceDeletionProcessor
    ) {
        $this->logger = $logger;
        $this->dataManager = $dataManager;
        $this->resourceDeletionProcessor = $resourceDeletionProcessor;
    }

    /**
     * {@inheritDoc}
     */
    public function runInsert(ContextDataInterface $contextDefinition, ResourceMetaInterface $resourceMeta)
    {
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
    public function runUpdate(ContextDataInterface $contextDefinition, ResourceMetaInterface $resourceMeta)
    {
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
    public function runDelete(ContextDataInterface $contextDefinition, ResourceMetaInterface $resourceMeta)
    {
        $this->resourceDeletionProcessor->processByResourceMeta($contextDefinition, $resourceMeta);
    }

}