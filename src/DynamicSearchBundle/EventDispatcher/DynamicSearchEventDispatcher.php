<?php

namespace DynamicSearchBundle\EventDispatcher;

use DynamicSearchBundle\EventSubscriber\DynamicSearchEventSubscriber;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\ImmutableEventDispatcher;

class DynamicSearchEventDispatcher implements DynamicSearchEventDispatcherInterface
{
    /**
     * @var ImmutableEventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @param DynamicSearchEventSubscriber $subscriber
     */
    public function __construct(DynamicSearchEventSubscriber $subscriber)
    {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($subscriber);

        $this->eventDispatcher = new ImmutableEventDispatcher($eventDispatcher);
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch($eventName, Event $event = null)
    {
        $this->eventDispatcher->dispatch($eventName, $event);
    }
}
