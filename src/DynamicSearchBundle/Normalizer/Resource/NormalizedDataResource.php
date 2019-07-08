<?php

namespace DynamicSearchBundle\Normalizer\Resource;

use DynamicSearchBundle\Transformer\Container\ResourceContainerInterface;

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
     * @var array
     */
    protected $options;

    /**
     * @param ResourceContainerInterface|null $resourceContainer
     * @param ResourceMetaInterface           $resourceMeta
     * @param array                           $options
     */
    public function __construct(?ResourceContainerInterface $resourceContainer, $resourceMeta, array $options = [])
    {
        $this->resourceContainer = $resourceContainer;
        $this->resourceMeta = $resourceMeta;
        $this->options = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function getResourceContainer()
    {
        return $this->resourceContainer;
    }

    /**
     * {@inheritDoc}
     */
    public function getResourceMeta()
    {
        return $this->resourceMeta;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return $this->options;
    }
}
