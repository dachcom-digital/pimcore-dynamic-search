<?php

namespace DynamicSearchBundle\EventSubscriber;

use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\DynamicSearchEvents;
use DynamicSearchBundle\Event\NewDataEvent;
use DynamicSearchBundle\Exception\DataTransformerException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\DataTransformerManagerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\Transformer\DataTransformerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DataProcessingEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var DataTransformerManagerInterface
     */
    protected $dataTransformerManager;

    /**
     * @var IndexManagerInterface
     */
    protected $indexManager;

    /**
     * @param LoggerInterface                 $logger
     * @param DataTransformerManagerInterface $dataTransformerManager
     * @param IndexManagerInterface           $indexManager
     */
    public function __construct(
        LoggerInterface $logger,
        DataTransformerManagerInterface $dataTransformerManager,
        IndexManagerInterface $indexManager
    ) {
        $this->logger = $logger;
        $this->dataTransformerManager = $dataTransformerManager;
        $this->indexManager = $indexManager;
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
        $dataTransformer = $this->dataTransformerManager->getDataTransformer($event->getProvider(), $event->getContextData(), $event->getData());

        if (!$dataTransformer instanceof DataTransformerInterface) {
            return;
        }

        try {
            $indexDocument = $dataTransformer->transformData($event->getContextData(), $event->getData());
        } catch (\Throwable $e) {
            throw new DataTransformerException(sprintf('Error while apply data transformation for "%s". Error was: %s', $dataTransformer->getAlias(), $e->getMessage()));
        }

        if (!$indexDocument instanceof IndexDocument) {
            return;
        }

        $this->logger->debug(
            sprintf('Index Document with %d fields successfully generated. Used "%s" transformer',
                count($indexDocument->getFields()), $dataTransformer->getAlias()
            ), $event->getProvider(), $event->getContextData()->getName());

        $indexProvider = $this->indexManager->getIndexProvider($event->getContextData());

        $indexProvider->executeInsert($event->getContextData(), $indexDocument);
    }

    public function updateDataInIndex(NewDataEvent $event)
    {
        $transformedData = $this->dataTransformerManager->getDataTransformer($event->getProvider(), $event->getContextData(), $event->getData());

        if (!$transformedData instanceof IndexDocument) {
            return;
        }

        $indexProvider = $this->indexManager->getIndexProvider($event->getContextData());

        $indexProvider->executeUpdate($event->getContextData(), $transformedData);
    }

    public function deleteDataFromIndex(NewDataEvent $event)
    {
        $transformedData = $this->dataTransformerManager->getDataTransformer($event->getProvider(), $event->getContextData(), $event->getData());

        if (!$transformedData instanceof IndexDocument) {
            return;
        }

        $indexProvider = $this->indexManager->getIndexProvider($event->getContextData());

        $indexProvider->executeDelete($event->getContextData(), $transformedData);
    }

}