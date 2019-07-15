<?php

namespace DynamicSearchBundle\EventSubscriber;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\DynamicSearchEvents;
use DynamicSearchBundle\Event\NewDataEvent;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Processor\ResourceModificationProcessorInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
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
     * @var ResourceModificationProcessorInterface
     */
    protected $resourceModificationProcessor;

    /**
     * @param LoggerInterface                        $logger
     * @param ConfigurationInterface                 $configuration
     * @param ResourceModificationProcessorInterface $resourceModificationProcessor
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        ResourceModificationProcessorInterface $resourceModificationProcessor
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->resourceModificationProcessor = $resourceModificationProcessor;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            DynamicSearchEvents::NEW_DATA_AVAILABLE => ['dispatchResourceModification'],
        ];
    }

    /**
     * @param NewDataEvent $event
     */
    public function dispatchResourceModification(NewDataEvent $event)
    {
        $contextDefinition = $this->configuration->getContextDefinition($event->getContextDispatchType(), $event->getContextName());

        if ($event->getProviderBehaviour() === DataProviderInterface::PROVIDER_BEHAVIOUR_FULL_DISPATCH) {
            $this->resourceModificationProcessor->process($contextDefinition, $event->getData());
        } elseif ($event->getProviderBehaviour() === DataProviderInterface::PROVIDER_BEHAVIOUR_SINGLE_DISPATCH) {
            $this->resourceModificationProcessor->processByResourceMeta($contextDefinition, $event->getResourceMeta(), $event->getData());
        } else {
            $this->logger->error(sprintf('Invalid provider behaviour "%s". Cannot dispatch resource processor', $event->getProviderBehaviour()), 'web_crawler', $event->getContextName());
        }
    }
}
