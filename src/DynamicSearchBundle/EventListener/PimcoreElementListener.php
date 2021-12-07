<?php

namespace DynamicSearchBundle\EventListener;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Queue\DataCollectorInterface;
use Pimcore\Event\AssetEvents;
use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\DocumentEvents;
use Pimcore\Event\Model\AssetEvent;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Event\Model\DocumentEvent;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PimcoreElementListener implements EventSubscriberInterface
{
    protected ConfigurationInterface $configuration;
    protected DataCollectorInterface $dataCollector;

    public function __construct(
        ConfigurationInterface $configuration,
        DataCollectorInterface $dataCollector
    ) {
        $this->configuration = $configuration;
        $this->dataCollector = $dataCollector;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DocumentEvents::POST_UPDATE => 'onDocumentPostUpdate',
            DocumentEvents::PRE_DELETE  => 'onDocumentPreDelete',

            DataObjectEvents::POST_UPDATE => 'onObjectPostUpdate',
            DataObjectEvents::PRE_DELETE  => 'onObjectPreDelete',

            AssetEvents::POST_ADD    => 'onAssetPostAdd',
            AssetEvents::POST_UPDATE => 'onAssetPostUpdate',
            AssetEvents::PRE_DELETE  => 'onAssetPreDelete',
        ];
    }

    public function onDocumentPostUpdate(DocumentEvent $event): void
    {
        if ($this->configuration->get('enable_pimcore_element_listener') === false) {
            return;
        }

        if ($event->getDocument()->getType() !== 'page') {
            return;
        }

        $dispatchType = $event->getDocument()->isPublished() === false
            ? ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_DELETE
            : ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_UPDATE;

        $this->dataCollector->addToGlobalQueue(
            $dispatchType,
            $event->getDocument()
        );
    }

    public function onDocumentPreDelete(DocumentEvent $event): void
    {
        if ($this->configuration->get('enable_pimcore_element_listener') === false) {
            return;
        }

        if ($event->getDocument()->getType() !== 'page') {
            return;
        }

        $this->dataCollector->addToGlobalQueue(
            ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_DELETE,
            $event->getDocument()
        );
    }

    public function onObjectPostUpdate(DataObjectEvent $event): void
    {
        if ($this->configuration->get('enable_pimcore_element_listener') === false) {
            return;
        }

        /** @var Concrete $object */
        $object = $event->getObject();

        $dispatchType = method_exists($object, 'isPublished')
            ? $object->isPublished() === false
                ? ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_DELETE
                : ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_UPDATE
            : ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_UPDATE;

        $this->dataCollector->addToGlobalQueue(
            $dispatchType,
            $event->getObject()
        );
    }

    public function onObjectPreDelete(DataObjectEvent $event): void
    {
        if ($this->configuration->get('enable_pimcore_element_listener') === false) {
            return;
        }

        $this->dataCollector->addToGlobalQueue(
            ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_DELETE,
            $event->getObject()
        );
    }

    public function onAssetPostAdd(AssetEvent $event): void
    {
        if ($this->configuration->get('enable_pimcore_element_listener') === false) {
            return;
        }

        $this->dataCollector->addToGlobalQueue(
            ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_INSERT,
            $event->getAsset()
        );
    }

    public function onAssetPostUpdate(AssetEvent $event): void
    {
        if ($this->configuration->get('enable_pimcore_element_listener') === false) {
            return;
        }

        $this->dataCollector->addToGlobalQueue(
            ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_UPDATE,
            $event->getAsset()
        );
    }

    public function onAssetPreDelete(AssetEvent $event): void
    {
        if ($this->configuration->get('enable_pimcore_element_listener') === false) {
            return;
        }

        $this->dataCollector->addToGlobalQueue(
            ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_DELETE,
            $event->getAsset()
        );
    }
}
