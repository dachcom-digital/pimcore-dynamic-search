<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface ResourceDeletionProcessorInterface
{
    /**
     * @param ContextDataInterface $contextData
     * @param mixed                $resource
     */
    public function process(ContextDataInterface $contextData, $resource);

    /**
     * @param ContextDataInterface  $contextData
     * @param ResourceMetaInterface $resourceMeta
     */
    public function processByResourceMeta(ContextDataInterface $contextData, ResourceMetaInterface $resourceMeta);
}
