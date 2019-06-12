<?php

namespace DynamicSearchBundle\EventSubscriber;

use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\DynamicSearchEvents;
use DynamicSearchBundle\Event\NewDataEvent;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\DataTransformerManagerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DynamicSearchEventSubscriber implements EventSubscriberInterface
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
        $transformedData = $this->dataTransformerManager->execute($event->getProvider(), $event->getContextData(), $event->getData());

        if (!$transformedData instanceof IndexDocument) {
            return;
        }

        $indexProvider = $this->indexManager->getIndexManger($event->getContextData());

        $indexProvider->executeInsert($event->getContextData(), $transformedData);
    }

    public function updateDataInIndex(NewDataEvent $event)
    {
        $transformedData = $this->dataTransformerManager->execute($event->getProvider(), $event->getContextData(), $event->getData());

        if (!$transformedData instanceof IndexDocument) {
            return;
        }

        $indexProvider = $this->indexManager->getIndexManger($event->getContextData());

        $indexProvider->executeUpdate($event->getContextData(), $transformedData);
    }

    public function deleteDataFromIndex(NewDataEvent $event)
    {
        $transformedData = $this->dataTransformerManager->execute($event->getProvider(), $event->getContextData(), $event->getData());

        if (!$transformedData instanceof IndexDocument) {
            return;
        }

        $indexProvider = $this->indexManager->getIndexManger($event->getContextData());

        $indexProvider->executeDelete($event->getContextData(), $transformedData);
    }

}