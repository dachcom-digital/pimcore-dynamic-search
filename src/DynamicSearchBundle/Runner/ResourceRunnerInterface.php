<?php

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Exception\SilentException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface ResourceRunnerInterface
{
    /**
     * @param string                $contextName
     * @param ResourceMetaInterface $resourceMeta
     *
     * @throws SilentException
     */
    public function runInsert(string $contextName, ResourceMetaInterface $resourceMeta);

    /**
     * @param string                        $contextName
     * @param array|ResourceMetaInterface[] $resourceMetaStack
     *
     * @throws SilentException
     */
    public function runInsertStack(string $contextName, array $resourceMetaStack);

    /**
     * @param string                $contextName
     * @param ResourceMetaInterface $resourceMeta
     *
     * @throws SilentException
     */
    public function runUpdate(string $contextName, ResourceMetaInterface $resourceMeta);

    /**
     * @param string                        $contextName
     * @param array|ResourceMetaInterface[] $resourceMetaStack
     *
     * @throws SilentException
     */
    public function runUpdateStack(string $contextName, array $resourceMetaStack);

    /**
     * @param string                $contextName
     * @param ResourceMetaInterface $resourceMeta
     *
     * @throws SilentException
     */
    public function runDelete(string $contextName, ResourceMetaInterface $resourceMeta);

    /**
     * @param string                        $contextName
     * @param array|ResourceMetaInterface[] $resourceMetaStack
     *
     * @throws SilentException
     */
    public function runDeleteStack(string $contextName, array $resourceMetaStack);
}
