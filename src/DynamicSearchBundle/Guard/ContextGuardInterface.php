<?php

namespace DynamicSearchBundle\Guard;

use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface ContextGuardInterface
{
    public function verifyResourceMetaForContext(string $contextName, ResourceMetaInterface $resourceMeta): bool;
}
