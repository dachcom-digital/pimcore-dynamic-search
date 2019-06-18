<?php

namespace DynamicSearchBundle\EventSubscriber;

use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\DynamicSearchEvents;
use DynamicSearchBundle\Event\NewDataEvent;
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
     * @param TransformerManagerInterface           $dataTransformerManager
     * @param IndexManagerInterface                 $indexManager
     * @param TransformerWorkflowProcessorInterface $transformerWorkflowProcessor
     */
    public function __construct(
        LoggerInterface $logger,
        TransformerManagerInterface $dataTransformerManager,
        IndexManagerInterface $indexManager,
        TransformerWorkflowProcessorInterface $transformerWorkflowProcessor
    ) {
        $this->logger = $logger;
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
            DynamicSearchEvents::NEW_DATA_AVAILABLE => ['addDataToIndex'],
            //DynamicSearchEvents::UPDATED_DATA_AVAILABLE => ['updateDataInIndex'],
            //DynamicSearchEvents::REMOVED_DATA_AVAILABLE => ['deleteDataFromIndex']
        ];
    }

    public function addDataToIndex(NewDataEvent $event)
    {
        $indexProvider = $this->indexManager->getIndexProvider($event->getContextData());

        try {
            $indexDocument = $this->transformerWorkflowProcessor->dispatchIndexDocumentTransform($event->getContextData(), $event->getData());
        } catch (\Throwable $e) {
            throw new TransformerException(sprintf('Error while apply data transformation. Error was: %s', $e->getMessage()));
        }

        if (!$indexDocument instanceof IndexDocument) {
            return;
        }

        $this->logger->debug(
            sprintf('Index Document with %d fields successfully generated. Used "%s" transformer',
                count($indexDocument->getFields()), $indexDocument->getDispatchedTransformerName()
            ), $event->getContextData()->getIndexProvider(), $event->getContextData()->getName());

        $indexProvider->executeInsert($event->getContextData(), $indexDocument);

    }

    public function updateDataInIndex(NewDataEvent $event)
    {
       //$indexProvider->executeUpdate($event->getContextData(), $indexDocument);
    }

    public function deleteDataFromIndex(NewDataEvent $event)
    {
        //$indexProvider->executeDelete($event->getContextData(), $indexDocument);
    }

}