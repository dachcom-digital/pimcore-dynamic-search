<?php

namespace DynamicSearchBundle\Resource\Proxy;

/**
 * @deprecated since 1.0.0 and will be removed in 2.0.0.
 */
interface ProxyResourceInterface
{
    /**
     * @return mixed
     */
    public function getOriginalResource();

    /**
     * @return string
     */
    public function getOriginalContextDispatchType();

    /**
     * @param mixed $proxyResource
     */
    public function setProxyResource($proxyResource);

    /**
     * @return mixed|null
     */
    public function getProxyResource();

    /**
     * @return bool
     */
    public function hasProxyResource();

    /**
     * @param string $proxyContextDispatchType
     */
    public function setProxyContextDispatchType(string $proxyContextDispatchType);

    /**
     * @return string|null
     */
    public function getProxyContextDispatchType();

    /**
     * @return bool
     */
    public function hasProxyContextDispatchType();
}
