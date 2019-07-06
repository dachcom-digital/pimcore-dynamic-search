<?php

namespace DynamicSearchBundle\Normalizer\Resource;

use DynamicSearchBundle\Transformer\Container\ResourceContainerInterface;

class NormalizedDataResource implements NormalizedDataResourceInterface
{
    /**
     * @var ResourceContainerInterface
     */
    public $resourceContainer;

    /**
     * @var mixed
     */
    public $resourceId;

    /**
     * @var array
     */
    public $options;

    /**
     * @param ResourceContainerInterface|null $resourceContainer
     * @param                                 $resourceId
     * @param array                           $options
     */
    public function __construct(?ResourceContainerInterface $resourceContainer, $resourceId, array $options = [])
    {
        $this->resourceContainer = $resourceContainer;
        $this->resourceId = $resourceId;
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
    public function getResourceId()
    {
        return $this->resourceId;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return $this->options;
    }
}
