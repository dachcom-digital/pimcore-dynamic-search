<?php

namespace DynamicSearchBundle\Validator;

use DynamicSearchBundle\Resource\Proxy\ProxyResourceInterface;
use DynamicSearchBundle\Resource\ResourceCandidateInterface;

interface ResourceValidatorInterface
{
    /**
     * @deprecated since 1.0.0 and will be removed in 2.0.0
     */
    public function checkUntrustedResourceProxy(string $contextName, string $dispatchType, $resource): ?ProxyResourceInterface;

    /**
     * @deprecated since 1.0.0 and will be removed in 2.0.0
     */
    public function validateUntrustedResource(string $contextName, string $dispatchType, $resource): bool;

    public function validateResource(string $contextName, string $dispatchType, bool $isUnknownResource, bool $isImmutableResource, $resource): ResourceCandidateInterface;
}
