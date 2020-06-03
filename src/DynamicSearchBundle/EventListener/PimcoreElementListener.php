<?php

namespace DynamicSearchBundle\EventListener;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
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
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var DataCollectorInterface
     */
    protected $dataCollector;

    /**
     * @param ConfigurationInterface $configuration
     * @param DataCollectorInterface $dataCollector
     */
    public function __construct(
        ConfigurationInterface $configuration,
        DataCollectorInterface $dataCollector
    ) {
        $this->configuration = $configuration;
        $this->dataCollector = $dataCollector;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
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

    /**
     * @param DocumentEvent $event
     */
    public function onDocumentPostUpdate(DocumentEvent $event)
    {
        if ($this->configuration->get('enable_pimcore_element_listener') === false) {
            return;
        }

        if ($event->getDocument()->getType() !== 'page') {
            return;
        }

        $dispatchType = $event->getDocument()->isPublished() === false
            ? ContextDataInterface::CONTEXT_DISPATCH_TYPE_DELETE
            : ContextDataInterface::CONTEXT_DISPATCH_TYPE_UPDATE;

        $this->dataCollector->addToGlobalQueue(
            $dispatchType,
            $event->getDocument()
        );
    }

    /**
     * @param DocumentEvent $event
     */
    public function onDocumentPreDelete(DocumentEvent $event)
    {
        if ($this->configuration->get('enable_pimcore_element_listener') === false) {
            return;
        }

        if ($event->getDocument()->getType() !== 'page') {
            return;
        }

        $this->dataCollector->addToGlobalQueue(
            ContextDataInterface::CONTEXT_DISPATCH_TYPE_DELETE,
            $event->getDocument()
        );
    }

    /**
     * @param DataObjectEvent $event
     */
    public function onObjectPostUpdate(DataObjectEvent $event)
    {
        if ($this->configuration->get('enable_pimcore_element_listener') === false) {
            return;
        }

        /** @var Concrete $object */
        $object = $event->getObject();

        $dispatchType = method_exists($object, 'isPublished')
            ? $object->isPublished() === false
                ? ContextDataInterface::CONTEXT_DISPATCH_TYPE_DELETE
                : ContextDataInterface::CONTEXT_DISPATCH_TYPE_UPDATE
            : ContextDataInterface::CONTEXT_DISPATCH_TYPE_UPDATE;

        $this->dataCollector->addToGlobalQueue(
            $dispatchType,
            $event->getObject()
        );
    }

    /**
     * @param DataObjectEvent $event
     */
    public function onObjectPreDelete(DataObjectEvent $event)
    {
        if ($this->configuration->get('enable_pimcore_element_listener') === false) {
            return;
        }

        $this->dataCollector->addToGlobalQueue(
            ContextDataInterface::CONTEXT_DISPATCH_TYPE_DELETE,
            $event->getObject()
        );
    }

    /**
     * @param AssetEvent $event
     */
    public function onAssetPostAdd(AssetEvent $event)
    {
        if ($this->configuration->get('enable_pimcore_element_listener') === false) {
            return;
        }

        $this->dataCollector->addToGlobalQueue(
            ContextDataInterface::CONTEXT_DISPATCH_TYPE_INSERT,
            $event->getAsset()
        );
    }

    /**
     * @param AssetEvent $event
     */
    public function onAssetPostUpdate(AssetEvent $event)
    {
        if ($this->configuration->get('enable_pimcore_element_listener') === false) {
            return;
        }

        $this->dataCollector->addToGlobalQueue(
            ContextDataInterface::CONTEXT_DISPATCH_TYPE_UPDATE,
            $event->getAsset()
        );
    }

    /**
     * @param AssetEvent $event
     */
    public function onAssetPreDelete(AssetEvent $event)
    {
        if ($this->configuration->get('enable_pimcore_element_listener') === false) {
            return;
        }

        $this->dataCollector->addToGlobalQueue(
            ContextDataInterface::CONTEXT_DISPATCH_TYPE_DELETE,
            $event->getAsset()
        );
    }
}
