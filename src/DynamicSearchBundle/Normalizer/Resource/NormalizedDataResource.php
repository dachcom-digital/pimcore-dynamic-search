<?php

namespace DynamicSearchBundle\Normalizer\Resource;

use DynamicSearchBundle\Transformer\Container\DocumentContainerInterface;

class NormalizedDataResource implements NormalizedDataResourceInterface
{
    /**
     * @var DocumentContainerInterface
     */
    public $documentContainer;

    /**
     * @var mixed
     */
    public $resourceId;

    /**
     * @var array
     */
    public $options;

    /**
     * @param DocumentContainerInterface|null $documentContainer
     * @param                                 $resourceId
     * @param array                           $options
     */
    public function __construct(?DocumentContainerInterface $documentContainer, $resourceId, array $options = [])
    {
        $this->documentContainer = $documentContainer;
        $this->resourceId = $resourceId;
        $this->options = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function getDocumentContainer()
    {
        return $this->documentContainer;
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
