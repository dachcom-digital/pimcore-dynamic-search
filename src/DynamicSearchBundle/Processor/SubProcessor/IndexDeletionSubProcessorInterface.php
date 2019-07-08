<?php

namespace DynamicSearchBundle\Processor\SubProcessor;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface IndexDeletionSubProcessorInterface
{
    /**
     * @param ContextDataInterface  $contextData
     * @param ResourceMetaInterface $resourceMeta
     */
    public function dispatch(ContextDataInterface $contextData, ResourceMetaInterface $resourceMeta);
}
