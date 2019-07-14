<?php

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface ResourceRunnerInterface
{
    /**
     * @param string                $contextName
     * @param ResourceMetaInterface $resourceMeta
     */
    public function runInsert(string $contextName, ResourceMetaInterface $resourceMeta);

    /**
     * @param string                        $contextName
     * @param array|ResourceMetaInterface[] $resourceMetaStack
     */
    public function runInsertStack(string $contextName, array $resourceMetaStack);

    /**
     * @param string                $contextName
     * @param ResourceMetaInterface $resourceMeta
     */
    public function runUpdate(string $contextName, ResourceMetaInterface $resourceMeta);

    /**
     * @param string                        $contextName
     * @param array|ResourceMetaInterface[] $resourceMetaStack
     */
    public function runUpdateStack(string $contextName, array $resourceMetaStack);

    /**
     * @param string                $contextName
     * @param ResourceMetaInterface $resourceMeta
     */
    public function runDelete(string $contextName, ResourceMetaInterface $resourceMeta);

    /**
     * @param string                        $contextName
     * @param array|ResourceMetaInterface[] $resourceMetaStack
     */
    public function runDeleteStack(string $contextName, array $resourceMetaStack);
}
