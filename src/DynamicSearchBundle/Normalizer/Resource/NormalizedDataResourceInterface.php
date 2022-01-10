<?php

namespace DynamicSearchBundle\Normalizer\Resource;

use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;

interface NormalizedDataResourceInterface
{
    public function getResourceContainer(): ?ResourceContainerInterface;

    public function getResourceMeta(): ResourceMetaInterface;
}
