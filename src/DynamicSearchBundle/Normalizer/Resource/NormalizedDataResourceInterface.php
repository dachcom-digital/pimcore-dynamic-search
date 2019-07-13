<?php

namespace DynamicSearchBundle\Normalizer\Resource;

use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;

interface NormalizedDataResourceInterface
{
    /**
     * @return ResourceContainerInterface|null
     */
    public function getResourceContainer();

    /**
     * @return ResourceMetaInterface
     */
    public function getResourceMeta();
}
