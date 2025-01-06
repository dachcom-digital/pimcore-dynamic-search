<?php

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Exception\SilentException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface ResourceRunnerInterface
{
    /**
     * @throws SilentException
     */
    public function runResourceStack(string $contextName, string $dispatchType, array $resourceMetaStack): void;

    /**
     * @throws SilentException
     */
    public function runResource(string $contextName, string $dispatchType, ResourceMetaInterface $resourceMeta): void;

    /**
     * @throws SilentException
     */
    public function runDelete(string $contextName, ResourceMetaInterface $resourceMeta): void;

    /**
     * @throws SilentException
     */
    public function runDeleteStack(string $contextName, array $resourceMetaStack): void;
}
