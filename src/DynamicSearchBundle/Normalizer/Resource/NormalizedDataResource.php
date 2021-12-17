<?php

namespace DynamicSearchBundle\Normalizer\Resource;

use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;

class NormalizedDataResource implements NormalizedDataResourceInterface
{
    protected ?ResourceContainerInterface $resourceContainer;
    protected ResourceMetaInterface $resourceMeta;

    public function __construct(?ResourceContainerInterface $resourceContainer, $resourceMeta)
    {
        $this->resourceContainer = $resourceContainer;
        $this->resourceMeta = $resourceMeta;
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
