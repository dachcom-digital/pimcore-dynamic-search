<?php

namespace DynamicSearchBundle\EventSubscriber;

use DynamicSearchBundle\Paginator\AdapterInterface;
use Knp\Component\Pager\Event\ItemsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class PaginateRawResultSubscriber implements EventSubscriberInterface
{
    public function items(ItemsEvent $event): void
    {
        $adapter = $event->target;

        if(!$adapter instanceof AdapterInterface) {
            return;
        }

        $event->count = $adapter->getCount();
        $event->items = $adapter->getItems($event->getOffset(), $event->getLimit());

        $event->stopPropagation();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'knp_pager.items' => ['items', 0]
        ];
    }
}