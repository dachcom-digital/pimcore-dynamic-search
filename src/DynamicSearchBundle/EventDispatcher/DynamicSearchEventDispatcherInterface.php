<?php

namespace DynamicSearchBundle\EventDispatcher;

use Symfony\Contracts\EventDispatcher\Event;

interface DynamicSearchEventDispatcherInterface
{
    /**
     * @param string     $eventName
     * @param Event|null $event
     */
    public function dispatch($eventName, Event $event = null);
}
