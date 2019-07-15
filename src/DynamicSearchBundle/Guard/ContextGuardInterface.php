<?php

namespace DynamicSearchBundle\Guard;

use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface ContextGuardInterface
{
    /**
     * @param string                $contextName
     * @param ResourceMetaInterface $resourceMeta
     *
     * @return bool
     */
    public function verifyResourceMetaForContext(string $contextName, ResourceMetaInterface $resourceMeta);
}
