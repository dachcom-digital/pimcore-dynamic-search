<?php

namespace DynamicSearchBundle\Resource\Proxy;

class ProxyResource implements ProxyResourceInterface
{
    /**
     * @var mixed
     */
    protected $resource;

    /**
     * @var string
     */
    protected $contextDispatchType;

    /**
     * @var string
     */
    protected $contextName;

    /**
     * @var mixed|null
     */
    protected $proxyResource;

    /**
     * @var string|null
     */
    protected $proxyContextDispatchType;

    /**
     * @param mixed  $resource
     * @param string $contextDispatchType
     * @param string $contextName
     */
    public function __construct($resource, string $contextDispatchType, string $contextName)
    {
        $this->resource = $resource;
        $this->contextDispatchType = $contextDispatchType;
        $this->contextName = $contextName;
    }

    /**
     * {@inheritDoc}
     */
    public function getOriginalResource()
    {
        return $this->resource;
    }

    /**
     * {@inheritDoc}
     */
    public function getOriginalContextDispatchType()
    {
        return $this->contextDispatchType;
    }

    /**
     * {@inheritDoc}
     */
    public function setProxyResource($proxyResource)
    {
        $this->proxyResource = $proxyResource;
    }

    /**
     * {@inheritDoc}
     */
    public function getProxyResource()
    {
        return $this->proxyResource;
    }

    /**
     * {@inheritDoc}
     */
    public function hasProxyResource()
    {
        return $this->proxyResource !== null;
    }

    /**
     * {@inheritDoc}
     */
    public function setProxyContextDispatchType(string $proxyContextDispatchType)
    {
        $this->proxyContextDispatchType = $proxyContextDispatchType;
    }

    /**
     * {@inheritDoc}
     */
    public function getProxyContextDispatchType()
    {
        return $this->proxyContextDispatchType;
    }

    /**
     * {@inheritDoc}
     */
    public function hasProxyContextDispatchType()
    {
        return $this->proxyContextDispatchType !== null;
    }
}
