<?php

namespace DynamicSearchBundle\Transformer\Container;

class ResourceContainer implements ResourceContainerInterface
{
    /**
     * @var mixed
     */
    protected $resource;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @param mixed $resource
     * @param array $attributes
     */
    public function __construct($resource, array $attributes = [])
    {
        $this->resource = $resource;
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
    public function getResource()
    {
        return $this->resource;
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
