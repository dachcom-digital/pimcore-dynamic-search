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
    public $resourceCollectionType;

    /**
     * @var mixed
     */
    public $resourceType;

    /**
     * @var mixed
     */
    public $resourceId;

    /**
     * @param mixed  $documentId
     * @param string $resourceCollectionType
     * @param string $resourceType
     * @param mixed  $resourceId
     */
    public function __construct(
        $documentId,
        $resourceId,
        string $resourceCollectionType,
        string $resourceType
    ) {
        $this->documentId = $documentId;
        $this->resourceId = $resourceId;
        $this->resourceCollectionType = $resourceCollectionType;
        $this->resourceType = $resourceType;
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
}
