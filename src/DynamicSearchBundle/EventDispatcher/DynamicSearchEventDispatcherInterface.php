<?php

namespace DynamicSearchBundle\EventDispatcher;

use Symfony\Contracts\EventDispatcher\Event;

interface DynamicSearchEventDispatcherInterface
{
    /**
     * @param Event  $event
     * @param string $eventName
     */
    public function dispatch(Event $event, $eventName);
}
