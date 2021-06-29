<?php

namespace DynamicSearchBundle\EventDispatcher;

use DynamicSearchBundle\EventSubscriber\DataProcessingEventSubscriber;
use DynamicSearchBundle\EventSubscriber\ErrorEventSubscriber;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\ImmutableEventDispatcher;
use Symfony\Contracts\EventDispatcher\Event;

class DynamicSearchEventDispatcher implements DynamicSearchEventDispatcherInterface
{
    protected ImmutableEventDispatcher $eventDispatcher;

    public function __construct(
        DataProcessingEventSubscriber $dataProcessingEventSubscriber,
        ErrorEventSubscriber $errorEventSubscriber
    ) {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($dataProcessingEventSubscriber);
        $eventDispatcher->addSubscriber($errorEventSubscriber);

        $this->eventDispatcher = new ImmutableEventDispatcher($eventDispatcher);
    }

    public function dispatch(Event $event, string $eventName): void
    {
        $this->eventDispatcher->dispatch($event, $eventName);
    }
}
