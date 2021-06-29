<?php

namespace DynamicSearchBundle\Resource\Proxy;

/**
 * @deprecated since 1.0.0 and will be removed in 2.0.0.
 */
class ProxyResource implements ProxyResourceInterface
{
    /**
     * @var mixed
     */
    protected $resource;

    protected string $contextDispatchType;
    protected string $contextName;
    protected $proxyResource;
    protected ?string $proxyContextDispatchType = null;

    public function __construct($resource, string $contextDispatchType, string $contextName)
    {
        $this->resource = $resource;
        $this->contextDispatchType = $contextDispatchType;
        $this->contextName = $contextName;
    }

    public function getOriginalResource()
    {
        return $this->resource;
    }

    public function getOriginalContextDispatchType(): string
    {
        return $this->contextDispatchType;
    }

    public function setProxyResource($proxyResource): void
    {
        $this->proxyResource = $proxyResource;
    }

    public function getProxyResource()
    {
        return $this->proxyResource;
    }

    public function hasProxyResource(): bool
    {
        return $this->proxyResource !== null;
    }

    public function setProxyContextDispatchType(string $proxyContextDispatchType): void
    {
        $this->proxyContextDispatchType = $proxyContextDispatchType;
    }

    public function getProxyContextDispatchType(): string
    {
        return $this->proxyContextDispatchType;
    }

    public function hasProxyContextDispatchType(): bool
    {
        return $this->proxyContextDispatchType !== null;
    }
}
