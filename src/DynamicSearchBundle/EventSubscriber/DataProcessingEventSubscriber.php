<?php

namespace DynamicSearchBundle\EventSubscriber;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\DynamicSearchEvents;
use DynamicSearchBundle\Event\NewDataEvent;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Processor\SubProcessor\IndexModificationSubProcessorInterface;
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
     * @var IndexModificationSubProcessorInterface
     */
    protected $indexModificationSubProcessor;

    /**
     * @param LoggerInterface                        $logger
     * @param ConfigurationInterface                 $configuration
     * @param IndexModificationSubProcessorInterface $indexModificationSubProcessor
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        IndexModificationSubProcessorInterface $indexModificationSubProcessor
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->indexModificationSubProcessor = $indexModificationSubProcessor;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            DynamicSearchEvents::NEW_DATA_AVAILABLE => ['dispatchIndexProviderInsert'],
        ];
    }

    /**
     * @param NewDataEvent $event
     */
    public function dispatchIndexProviderInsert(NewDataEvent $event)
    {
        $contextDefinition = $this->configuration->getContextDefinition($event->getContextDispatchType(), $event->getContextName(), $event->getRuntimeValues());

        $this->indexModificationSubProcessor->dispatch($contextDefinition, $event->getData());
    }
}