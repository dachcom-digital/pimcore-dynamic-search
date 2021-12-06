<?php

namespace DynamicSearchBundle\Validator;

use DynamicSearchBundle\Resource\Proxy\ProxyResourceInterface;
use DynamicSearchBundle\Resource\ResourceCandidateInterface;

interface ResourceValidatorInterface
{
    /**
     * @deprecated since 1.0.0 and will be removed in 2.0.0
     *
     * @param string $contextName
     * @param string $dispatchType
     * @param mixed  $resource
     *
     * @return ProxyResourceInterface|null
     */
    public function checkUntrustedResourceProxy(string $contextName, string $dispatchType, $resource);

    /**
     * @deprecated since 1.0.0 and will be removed in 2.0.0
     *
     * @param string $contextName
     * @param string $dispatchType
     * @param mixed  $resource
     *
     * @return bool
     */
    public function validateUntrustedResource(string $contextName, string $dispatchType, $resource);

    public function validateResource(string $contextName, string $dispatchType, bool $isUnknownResource, bool $isImmutableResource, mixed $resource): ResourceCandidateInterface;
}
