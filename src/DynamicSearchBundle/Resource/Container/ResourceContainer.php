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
     * {@inheritDoc}
     */
    public function hasResource()
    {
        return $this->resource !== null;
    }

    /**
     * {@inheritDoc}
     */
    public function isBaseResource()
    {
        return $this->isBaseResource === true;
    }

    /**
     * {@inheritDoc}
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * {@inheritDoc}
     */
    public function getResourceScaffolderIdentifier()
    {
        return $this->resourceScaffolderIdentifier;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAttribute($attribute)
    {
        return isset($this->attributes[$attribute]);
    }

    /**
     * {@inheritDoc}
     */
    public function getAttribute($attribute)
    {
        return $this->attributes[$attribute];
    }

    /**
     * {@inheritDoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
