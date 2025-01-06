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
