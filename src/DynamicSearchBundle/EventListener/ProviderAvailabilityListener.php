<?php

namespace DynamicSearchBundle\EventListener;

use DynamicSearchBundle\Manager\ProviderBundleManagerInterface;
use DynamicSearchBundle\Provider\Extension\ProviderBundleLocatorInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProviderAvailabilityListener implements EventSubscriberInterface
{
    /**
     * @var ProviderBundleLocatorInterface
     */
    protected $providerBundleManager;

    /**
     * @param ProviderBundleManagerInterface $providerBundleManager
     */
    public function __construct(ProviderBundleManagerInterface $providerBundleManager)
    {
        $this->providerBundleManager = $providerBundleManager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::COMMAND => 'onConsoleCommand'
        ];
    }

    /**
     * @param ConsoleCommandEvent $event
     */
    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        $command = $event->getCommand();

        if ($command->getName() !== 'cache:clear') {
            return;
        }

        $this->providerBundleManager->checkAvailableProviderBundles();
    }

}
