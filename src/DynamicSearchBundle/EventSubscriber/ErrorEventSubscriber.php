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
    /**
     * @var bool
     */
    protected $dispatched;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var IndexManagerInterface
     */
    protected $indexManager;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->dispatched = false;
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            DynamicSearchEvents::ERROR_DISPATCH_ABORT    => ['onAbort'],
            DynamicSearchEvents::ERROR_DISPATCH_CRITICAL => ['onCritical'],
        ];
    }

    /**
     * @param ErrorEvent $event
     *
     * @throws ProcessCancelledException
     */
    public function onAbort(ErrorEvent $event)
    {
        $this->logger->error($event->getMessage(), $event->getProviderName(), $event->getContextName());

        throw new ProcessCancelledException($event->getMessage());
    }

    /**
     * @param ErrorEvent $event
     *
     * @throws RuntimeException
     */
    public function onCritical(ErrorEvent $event)
    {
        if ($this->dispatched === true) {
            return;
        }

        $this->logger->error($event->getMessage(), $event->getProviderName(), $event->getContextName());

        throw new RuntimeException($event->getMessage());
    }
}