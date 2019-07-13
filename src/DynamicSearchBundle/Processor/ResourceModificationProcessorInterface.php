<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\RuntimeException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface ResourceModificationProcessorInterface
{
    /**
     * @param ContextDataInterface $contextData
     * @param mixed                $resource
     *
     * @throws RuntimeException
     */
    public function process(ContextDataInterface $contextData, $resource);

    /**
     * @param ContextDataInterface  $contextData
     * @param ResourceMetaInterface $resourceMeta
     * @param mixed                 $resource
     *
     * @throws RuntimeException
     */
    public function processByResourceMeta(ContextDataInterface $contextData, ResourceMetaInterface $resourceMeta, $resource);
}
