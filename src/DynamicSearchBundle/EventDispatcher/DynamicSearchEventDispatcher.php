<?php

namespace DynamicSearchBundle\EventDispatcher;

use DynamicSearchBundle\EventSubscriber\DataProcessingEventSubscriber;
use DynamicSearchBundle\EventSubscriber\ErrorEventSubscriber;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\ImmutableEventDispatcher;
use Symfony\Contracts\EventDispatcher\Event;

class DynamicSearchEventDispatcher implements DynamicSearchEventDispatcherInterface
{
    /**
     * @var ImmutableEventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @param DataProcessingEventSubscriber $dataProcessingEventSubscriber
     * @param ErrorEventSubscriber          $errorEventSubscriber
     */
    public function __construct(
        DataProcessingEventSubscriber $dataProcessingEventSubscriber,
        ErrorEventSubscriber $errorEventSubscriber
    ) {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($dataProcessingEventSubscriber);
        $eventDispatcher->addSubscriber($errorEventSubscriber);

        $this->eventDispatcher = new ImmutableEventDispatcher($eventDispatcher);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(Event $event, $eventName)
    {
        $this->eventDispatcher->dispatch($event, $eventName);
    }
}
