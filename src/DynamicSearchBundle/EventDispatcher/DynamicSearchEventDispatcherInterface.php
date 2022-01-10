<?php

namespace DynamicSearchBundle\EventDispatcher;

use Symfony\Contracts\EventDispatcher\Event;

interface DynamicSearchEventDispatcherInterface
{
    public function dispatch(Event $event, string $eventName): void;
}
