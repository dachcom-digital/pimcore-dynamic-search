<?php

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Exception\SilentException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface ResourceRunnerInterface
{
    public function runInsert(string $contextName, ResourceMetaInterface $resourceMeta): void;

    public function runInsertStack(string $contextName, array $resourceMetaStack): void;

    public function runUpdate(string $contextName, ResourceMetaInterface $resourceMeta): void;

    public function runUpdateStack(string $contextName, array $resourceMetaStack): void;

    public function runDelete(string $contextName, ResourceMetaInterface $resourceMeta): void;

    public function runDeleteStack(string $contextName, array $resourceMetaStack): void;
}
