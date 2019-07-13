<?php

namespace DynamicSearchBundle\Normalizer\Resource;

class ResourceMeta implements ResourceMetaInterface
{
    /**
     * @var mixed
     */
    public $documentId;

    /**
     * @var mixed
     */
    public $resourceId;

    /**
     * @var mixed
     */
    public $resourceCollectionType;

    /**
     * @var mixed
     */
    public $resourceType;

    /**
     * @var array
     */
    public $resourceOptions;

    /**
     * @var array
     */
    public $normalizerOptions;

    /**
     * @param mixed  $documentId
     * @param string $resourceCollectionType
     * @param string $resourceType
     * @param mixed  $resourceId
     * @param array  $resourceOptions
     * @param array  $normalizerOptions
     */
    public function __construct(
        $documentId,
        $resourceId,
        string $resourceCollectionType,
        string $resourceType,
        array $resourceOptions = [],
        array $normalizerOptions = []
    ) {
        $this->documentId = $documentId;
        $this->resourceId = $resourceId;
        $this->resourceCollectionType = $resourceCollectionType;
        $this->resourceType = $resourceType;
        $this->resourceOptions = $resourceOptions;
        $this->normalizerOptions = $normalizerOptions;
    }

    /**
     * {@inheritDoc}
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * {@inheritDoc}
     */
    public function getResourceCollectionType()
    {
        return $this->resourceCollectionType;
    }

    /**
     * {@inheritDoc}
     */
    public function getResourceType()
    {
        return $this->resourceType;
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
    public function getResourceOptions()
    {
        return $this->resourceOptions;
    }

    /**
     * {@inheritDoc}
     */
    public function hasNormalizerOption(string $option)
    {
        return isset($this->normalizerOptions[$option]);
    }

    /**
     * {@inheritDoc}
     */
    public function getNormalizerOption(string $option)
    {
        return $this->normalizerOptions[$option];
    }

    /**
     * {@inheritDoc}
     */
    public function getNormalizerOptions()
    {
        return is_array($this->normalizerOptions) ? $this->normalizerOptions : [];
    }
}
