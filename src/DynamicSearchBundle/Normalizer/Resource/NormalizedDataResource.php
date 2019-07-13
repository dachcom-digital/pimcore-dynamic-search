<?php

namespace DynamicSearchBundle\Normalizer\Resource;

use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;

class NormalizedDataResource implements NormalizedDataResourceInterface
{
    /**
     * @var ResourceContainerInterface
     */
    protected $resourceContainer;

    /**
     * @var ResourceMetaInterface
     */
    protected $resourceMeta;

    /**
     * @param ResourceContainerInterface|null $resourceContainer
     * @param ResourceMetaInterface           $resourceMeta
     */
    public function __construct(?ResourceContainerInterface $resourceContainer, $resourceMeta)
    {
        $this->resourceContainer = $resourceContainer;
        $this->resourceMeta = $resourceMeta;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceContainer()
    {
        return $this->resourceContainer;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceMeta()
    {
        return $this->resourceMeta;
    }
}
