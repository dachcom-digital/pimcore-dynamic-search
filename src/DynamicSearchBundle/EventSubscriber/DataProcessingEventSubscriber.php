<?php

namespace DynamicSearchBundle\EventSubscriber;

use DynamicSearchBundle\Builder\ContextDefinitionBuilderInterface;
use DynamicSearchBundle\DynamicSearchEvents;
use DynamicSearchBundle\Event\NewDataEvent;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Processor\ResourceModificationProcessorInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Validator\ResourceValidatorInterface;
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
     * @var ResourceValidatorInterface
     */
    protected $resourceValidator;

    /**
     * @param LoggerInterface                        $logger
     * @param ContextDefinitionBuilderInterface      $contextDefinitionBuilder
     * @param ResourceModificationProcessorInterface $resourceModificationProcessor
     * @param ResourceValidatorInterface             $resourceValidator
     */
    public function __construct(
        LoggerInterface $logger,
        ContextDefinitionBuilderInterface $contextDefinitionBuilder,
        ResourceModificationProcessorInterface $resourceModificationProcessor,
        ResourceValidatorInterface $resourceValidator
    ) {
        $this->logger = $logger;
        $this->contextDefinitionBuilder = $contextDefinitionBuilder;
        $this->resourceModificationProcessor = $resourceModificationProcessor;
        $this->resourceValidator = $resourceValidator;
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

        try {
            // validate and allow rewriting resource based on current data behaviour
            $isImmutableResource = $event->getProviderBehaviour() === DataProviderInterface::PROVIDER_BEHAVIOUR_SINGLE_DISPATCH;
            $resourceCandidate = $this->resourceValidator->validateResource($event->getContextName(), $event->getContextDispatchType(), false, $isImmutableResource, $event->getData());
        } catch (\Throwable $e) {
            $this->logger->error(
                sprintf(
                    'Error while validate resource candidate: %s',
                    $e->getMessage()), $contextDefinition->getDataProviderName(), $event->getContextName()
            );

            return;
        }

        if ($resourceCandidate->getResource() === null) {
            $this->logger->debug(
                sprintf(
                    'Resource has been removed due to validation. Skipping...'),
                $contextDefinition->getDataProviderName(), $contextDefinition->getName()
            );

            return;
        }

        if ($event->getProviderBehaviour() === DataProviderInterface::PROVIDER_BEHAVIOUR_FULL_DISPATCH) {
            $this->resourceModificationProcessor->process($contextDefinition, $resourceCandidate->getResource());
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
