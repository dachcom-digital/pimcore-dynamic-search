<?php

namespace DynamicSearchBundle\Resource\Container;

class ResourceContainer implements ResourceContainerInterface
{
    /**
     * @var mixed
     */
    protected $resource;

    /**
     * @var bool
     */
    protected $isBaseResource;

    /**
     * @var string
     */
    protected $resourceScaffolderIdentifier;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @param mixed  $resource
     * @param bool   $isBaseResource
     * @param string $resourceScaffolderIdentifier
     * @param array  $attributes
     */
    public function __construct($resource, bool $isBaseResource, $resourceScaffolderIdentifier, array $attributes = [])
    {
        $this->resource = $resource;
        $this->isBaseResource = $isBaseResource;
        $this->resourceScaffolderIdentifier = $resourceScaffolderIdentifier;
        $this->attributes = $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function hasResource()
    {
        return $this->resource !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function isBaseResource()
    {
        return $this->isBaseResource === true;
    }

    /**
     * {@inheritdoc}
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceScaffolderIdentifier()
    {
        return $this->resourceScaffolderIdentifier;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttribute($attribute)
    {
        return isset($this->attributes[$attribute]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($attribute)
    {
        return $this->attributes[$attribute];
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
