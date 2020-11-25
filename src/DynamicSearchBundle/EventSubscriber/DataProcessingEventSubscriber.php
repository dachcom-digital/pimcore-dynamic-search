<?php

namespace DynamicSearchBundle\EventSubscriber;

use DynamicSearchBundle\Builder\ContextDefinitionBuilderInterface;
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
     * @var ContextDefinitionBuilderInterface
     */
    protected $contextDefinitionBuilder;

    /**
     * @var ResourceModificationProcessorInterface
     */
    protected $resourceModificationProcessor;

    /**
     * @param LoggerInterface                        $logger
     * @param ContextDefinitionBuilderInterface      $contextDefinitionBuilder
     * @param ResourceModificationProcessorInterface $resourceModificationProcessor
     */
    public function __construct(
        LoggerInterface $logger,
        ContextDefinitionBuilderInterface $contextDefinitionBuilder,
        ResourceModificationProcessorInterface $resourceModificationProcessor
    ) {
        $this->logger = $logger;
        $this->contextDefinitionBuilder = $contextDefinitionBuilder;
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
        $contextDefinition = $this->contextDefinitionBuilder->buildContextDefinition($event->getContextName(), $event->getContextDispatchType());

        if ($event->getProviderBehaviour() === DataProviderInterface::PROVIDER_BEHAVIOUR_FULL_DISPATCH) {
            $this->resourceModificationProcessor->process($contextDefinition, $event->getData());
        } elseif ($event->getProviderBehaviour() === DataProviderInterface::PROVIDER_BEHAVIOUR_SINGLE_DISPATCH) {
            $this->resourceModificationProcessor->processByResourceMeta($contextDefinition, $event->getResourceMeta(), $event->getData());
        } else {
            $this->logger->error(
                sprintf('Invalid provider behaviour "%s". Cannot dispatch resource processor', $event->getProviderBehaviour()),
                $contextDefinition->getDataProviderName(), $event->getContextName()
            );
        }
    }
}
