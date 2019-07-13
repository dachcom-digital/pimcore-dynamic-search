<?php

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface ResourceRunnerInterface
{
    /**
     * @param ContextDataInterface  $contextDefinition
     * @param ResourceMetaInterface $resourceMeta
     */
    public function runInsert(ContextDataInterface $contextDefinition, ResourceMetaInterface $resourceMeta);

    /**
     * @param ContextDataInterface  $contextDefinition
     * @param ResourceMetaInterface $resourceMeta
     */
    public function runUpdate(ContextDataInterface $contextDefinition, ResourceMetaInterface $resourceMeta);

    /**
     * @param ContextDataInterface  $contextDefinition
     * @param ResourceMetaInterface $resourceMeta
     */
    public function runDelete(ContextDataInterface $contextDefinition, ResourceMetaInterface $resourceMeta);

}