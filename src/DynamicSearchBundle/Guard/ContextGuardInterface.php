<?php

namespace DynamicSearchBundle\Guard;

use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface ContextGuardInterface
{
    /**
     * @param string                $contextName
     * @param string                $dataProviderName
     * @param array                 $dataProviderOptions
     * @param ResourceMetaInterface $resourceMeta
     * @param mixed                 $resource
     *
     * @return bool
     */
    public function isValidateDataResource(string $contextName, string $dataProviderName, array $dataProviderOptions, ResourceMetaInterface $resourceMeta, $resource);
}
