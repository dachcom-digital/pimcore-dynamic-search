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
     * @param mixed  $documentId
     * @param string $resourceCollectionType
     * @param string $resourceType
     * @param mixed  $resourceId
     * @param array  $resourceOptions
     */
    public function __construct(
        $documentId,
        $resourceId,
        string $resourceCollectionType,
        string $resourceType,
        array $resourceOptions = []
    ) {
        $this->documentId = $documentId;
        $this->resourceId = $resourceId;
        $this->resourceCollectionType = $resourceCollectionType;
        $this->resourceType = $resourceType;
        $this->resourceOptions = $resourceOptions;
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
    public function hasResourceOption(string $option)
    {
        return isset($this->resourceOptions[$option]);
    }

    /**
     * {@inheritDoc}
     */
    public function getResourceOption(string $option)
    {
        return $this->resourceOptions[$option];
    }
}
