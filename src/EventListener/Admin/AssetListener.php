<?php

namespace DynamicSearchBundle\EventListener\Admin;

use Pimcore\Event\BundleManager\PathsEvent;
use Pimcore\Event\BundleManagerEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AssetListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            BundleManagerEvents::JS_PATHS  => 'addJsFiles',
            BundleManagerEvents::CSS_PATHS => 'addCssFiles',
        ];
    }

    public function addJsFiles(PathsEvent $event): void
    {
        $event->addPaths([
            '/bundles/dynamicsearch/js/backend/startup.js',
            '/bundles/dynamicsearch/js/backend/settings.js',
        ]);
    }

    public function addCssFiles(PathsEvent $event): void
    {
        $event->addPaths([
            '/bundles/dynamicsearch/css/admin.css'
        ]);
    }
}
