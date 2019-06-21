<?php

namespace DynamicSearchBundle\EventSubscriber;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\DynamicSearchEvents;
use DynamicSearchBundle\Event\NewDataEvent;
use DynamicSearchBundle\Exception\ProcessCancelledException;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Exception\TransformerException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\TransformerManagerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\Processor\TransformerWorkflowProcessorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DataProcessingEventSubscriber implements EventSubscriberInterface
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
     * @var TransformerManagerInterface
     */
    protected $transformerManager;

    /**
     * @var IndexManagerInterface
     */
    protected $indexManager;

    /**
     * @var TransformerWorkflowProcessorInterface
     */
    protected $transformerWorkflowProcessor;

    /**
     * @param LoggerInterface                       $logger
     * @param ConfigurationInterface                $configuration
     * @param TransformerManagerInterface           $dataTransformerManager
     * @param IndexManagerInterface                 $indexManager
     * @param TransformerWorkflowProcessorInterface $transformerWorkflowProcessor
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        TransformerManagerInterface $dataTransformerManager,
        IndexManagerInterface $indexManager,
        TransformerWorkflowProcessorInterface $transformerWorkflowProcessor
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->transformerManager = $dataTransformerManager;
        $this->indexManager = $indexManager;
        $this->transformerWorkflowProcessor = $transformerWorkflowProcessor;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            DynamicSearchEvents::NEW_DATA_AVAILABLE => ['dispatchIndexProvider'],
        ];
    }

    /**
     * @param NewDataEvent $event
     *
     * @throws TransformerException
     * @throws ProcessCancelledException
     * @throws ProviderException
     */
    public function dispatchIndexProvider(NewDataEvent $event)
    {
        $contextDefinition = $this->configuration->getContextDefinition($event->getContextDispatchType(), $event->getContextName(), $event->getRuntimeOptions());

        $indexProvider = $this->indexManager->getIndexProvider($contextDefinition);

        try {
            $indexDocument = $this->transformerWorkflowProcessor->dispatchIndexDocumentTransform($contextDefinition, $event->getData());
        } catch (\Throwable $e) {
            throw new TransformerException(sprintf('Error while apply data transformation. Error was: %s', $e->getMessage()));
        }

        if (!$indexDocument instanceof IndexDocument) {
            $this->logger->error('Index Document invalid. Maybe there is no valid id field in document? Skipping...',
                $contextDefinition->getIndexProviderName(), $event->getContextName());
            return;
        }

        $contextDefinition->updateRuntimeOption('index_document', $indexDocument);

        $this->logger->debug(
            sprintf('Index Document with %d fields successfully generated. Used "%s" transformer',
                count($indexDocument->getFields()), $indexDocument->getDispatchedTransformerName()
            ), $contextDefinition->getIndexProviderName(), $event->getContextName());

        $indexProvider->execute($contextDefinition);
    }
}