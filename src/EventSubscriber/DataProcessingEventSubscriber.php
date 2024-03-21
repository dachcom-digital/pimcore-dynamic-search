<?php

namespace DynamicSearchBundle\EventSubscriber;

use DynamicSearchBundle\Builder\ContextDefinitionBuilderInterface;
use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\DynamicSearchEvents;
use DynamicSearchBundle\Event\NewDataEvent;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Processor\ResourceModificationProcessorInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Queue\DataCollectorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DataProcessingEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected LoggerInterface $logger,
        protected ContextDefinitionBuilderInterface $contextDefinitionBuilder,
        protected ResourceModificationProcessorInterface $resourceModificationProcessor,
        protected DataCollectorInterface $dataCollector
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DynamicSearchEvents::NEW_DATA_AVAILABLE => ['dispatchResourceModification'],
        ];
    }

    public function dispatchResourceModification(NewDataEvent $event): void
    {
        $contextDefinition = $this->contextDefinitionBuilder->buildContextDefinition($event->getContextName(), $event->getContextDispatchType());

        if ($event->getProviderBehaviour() === DataProviderInterface::PROVIDER_BEHAVIOUR_FULL_DISPATCH) {
            // data collector add to queue
            $this->dataCollector->addToContextQueue(
                $contextDefinition->getName(),
                ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_INDEX,
                $event->getData(),
                [
                    'resourceValidation' => [
                        'unknownResource' => false,
                        'immutableResource' => false
                    ]
                ]
            );
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
