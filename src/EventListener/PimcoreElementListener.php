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
use Pimcore\Model\DataObject;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Security\User\TokenStorageUserResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PimcoreElementListener implements EventSubscriberInterface
{
    public function __construct(
        protected ConfigurationInterface $configuration,
        protected DataCollectorInterface $dataCollector,
        protected TokenStorageUserResolver $userResolver
    ) {
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

        if (
            ($event->hasArgument('saveVersionOnly') && $event->getArgument('saveVersionOnly') === true) ||
            ($event->hasArgument('isAutoSave') && $event->getArgument('isAutoSave') === true)
        ) {
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

        if (
            ($event->hasArgument('saveVersionOnly') && $event->getArgument('saveVersionOnly') === true) ||
            ($event->hasArgument('isAutoSave') && $event->getArgument('isAutoSave') === true)
        ) {
            return;
        }

        /** @var DataObject\Concrete $object */
        $object = $event->getObject();

        // @deprecated since 5.0: published/unpublished must be handled by project-specific resource validation
        $dispatchType = ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_UPDATE;
        if (method_exists($object, 'isPublished') && $object->isPublished() === false) {
            $dispatchType = ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_DELETE;
        }

        $this->dataCollector->addToGlobalQueue(
            $dispatchType,
            $event->getObject()
        );

        $this->checkInheritanceIndex($object);

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

    protected function checkInheritanceIndex(ElementInterface $element): void
    {
        // currently, only objects are allowed.

        if (!$element instanceof DataObject\Concrete) {
            return;
        }

        $inheritanceConfiguration = $this->configuration->get('element_inheritance');

        if ($inheritanceConfiguration['enabled'] === false) {
            return;
        }

        $classDefinition = DataObject\ClassDefinition::getById($element->getClassId());

        if (!$classDefinition instanceof DataObject\ClassDefinition) {
            return;
        }

        if ($classDefinition->getAllowInherit() === false) {
            return;
        }

        // we mostly want to fetch child elements if this comes from a real user (e.g. from backend)
        // if user is null we're most probably in a CLI process which handles children/variants by itself!
        // this can be changed by set origin_dispatch to "all"
        if ($inheritanceConfiguration['origin_dispatch'] === 'user' && $this->userResolver->getUser() === null) {
            return;
        }

        $list = new DataObject\Listing();
        $list->setCondition('path LIKE ' . $list->quote($element->getRealFullPath() . '/%'));
        $list->setUnpublished(false);
        $list->setObjectTypes([DataObject\AbstractObject::OBJECT_TYPE_OBJECT, DataObject\AbstractObject::OBJECT_TYPE_VARIANT]);

        foreach ($list->getObjects() as $childObject) {
            $dispatchType = ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_UPDATE;
            // @deprecated since 5.0: published/unpublished must be handled by project-specific resource validation
            if (method_exists($childObject, 'isPublished') && $childObject->isPublished() === false) {
                $dispatchType = ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_DELETE;
            }

            $this->dataCollector->addToGlobalQueue($dispatchType, $childObject);
        }
    }

}
