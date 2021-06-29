<?php

namespace DynamicSearchBundle\Resource\Proxy;

/**
 * @deprecated since 1.0.0 and will be removed in 2.0.0.
 */
interface ProxyResourceInterface
{
    public function getOriginalResource();

    public function getOriginalContextDispatchType(): string;

    public function setProxyResource($proxyResource): void;

    public function getProxyResource();

    public function hasProxyResource(): bool;

    public function setProxyContextDispatchType(string $proxyContextDispatchType): void;

    public function getProxyContextDispatchType(): ?string;

    public function hasProxyContextDispatchType(): bool;
}
