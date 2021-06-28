<?php

namespace DynamicSearchBundle\EventSubscriber;

use DynamicSearchBundle\DynamicSearchEvents;
use DynamicSearchBundle\Event\ErrorEvent;
use DynamicSearchBundle\Exception\ProcessCancelledException;
use DynamicSearchBundle\Exception\RuntimeException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ErrorEventSubscriber implements EventSubscriberInterface
{
    protected bool $dispatched;
    protected LoggerInterface $logger;
    protected ?IndexManagerInterface $indexManager = null;

    public function __construct(LoggerInterface $logger)
    {
        $this->dispatched = false;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DynamicSearchEvents::ERROR_DISPATCH_ABORT    => ['onAbort'],
            DynamicSearchEvents::ERROR_DISPATCH_CRITICAL => ['onCritical'],
        ];
    }

    public function onAbort(ErrorEvent $event): void
    {
        $this->logger->error($event->getMessage(), $event->getProviderName(), $event->getContextName());

        throw new ProcessCancelledException($event->getMessage());
    }

    public function onCritical(ErrorEvent $event): void
    {
        if ($this->dispatched === true) {
            return;
        }

        $this->logger->error($event->getMessage(), $event->getProviderName(), $event->getContextName());

        throw new RuntimeException($event->getMessage());
    }
}
