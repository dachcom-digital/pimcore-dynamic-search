<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace DynamicSearchBundle\EventSubscriber;

use DynamicSearchBundle\Paginator\AdapterInterface;
use Knp\Component\Pager\Event\ItemsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class PaginateRawResultSubscriber implements EventSubscriberInterface
{
    public function items(ItemsEvent $event): void
    {
        $adapter = $event->target;

        if (!$adapter instanceof AdapterInterface) {
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
