<?php

namespace DynamicSearchBundle\Processor\SubProcessor;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Exception\RuntimeException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;

class IndexDeletionSubProcessor implements IndexDeletionSubProcessorInterface
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
     * @var IndexManagerInterface
     */
    protected $indexManager;

    /**
     * @param LoggerInterface        $logger
     * @param ConfigurationInterface $configuration
     * @param IndexManagerInterface  $indexManager
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        IndexManagerInterface $indexManager
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->indexManager = $indexManager;
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch(ContextDataInterface $contextData, IndexDocument $indexDocument)
    {
        try {
            $indexProvider = $this->indexManager->getIndexProvider($contextData);
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf('Unable to load index provider "%s".', $contextData->getIndexProviderName()));
        }

        try {
            $contextData->updateRuntimeValue('index_document', $indexDocument);
            $indexProvider->execute($contextData);
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf('Error while executing index deletion. Error was: "%s".', $e->getMessage()));
        }
    }
}
