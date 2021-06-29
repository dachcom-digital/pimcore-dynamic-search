<?php

namespace DynamicSearchBundle\EventListener;

use DynamicSearchBundle\Manager\ProviderBundleManagerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProviderAvailabilityListener implements EventSubscriberInterface
{
    protected ProviderBundleManagerInterface $providerBundleManager;

    public function __construct(ProviderBundleManagerInterface $providerBundleManager)
    {
        $this->providerBundleManager = $providerBundleManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => 'onConsoleCommand'
        ];
    }

    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $command = $event->getCommand();

        if ($command === null) {
            return;
        }

        if ($command->getName() !== 'cache:clear') {
            return;
        }

        $this->providerBundleManager->checkAvailableProviderBundles();
    }
}
