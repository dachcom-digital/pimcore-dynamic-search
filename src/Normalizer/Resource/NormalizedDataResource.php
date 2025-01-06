<?php

namespace DynamicSearchBundle\Normalizer\Resource;

use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;

class NormalizedDataResource implements NormalizedDataResourceInterface
{
    public function __construct(
        protected ?ResourceContainerInterface $resourceContainer,
        protected ResourceMetaInterface $resourceMeta
    ) {
    }

    public function getResourceContainer(): ?ResourceContainerInterface
    {
        return $this->resourceContainer;
    }

    public function getResourceMeta(): ResourceMetaInterface
    {
        return $this->resourceMeta;
    }
}
