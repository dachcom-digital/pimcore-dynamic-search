<?php

namespace DynamicSearchBundle\Validator;

use DynamicSearchBundle\Resource\Proxy\ProxyResourceInterface;

interface ResourceValidatorInterface
{
    /**
     * @param string $contextName
     * @param string $dispatchType
     * @param mixed  $resource
     *
     * @return ProxyResourceInterface|null
     */
    public function checkUntrustedResourceProxy(string $contextName, string $dispatchType, $resource);

    /**
     * @param string $contextName
     * @param string $dispatchType
     * @param mixed  $resource
     *
     * @return bool
     */
    public function validateUntrustedResource(string $contextName, string $dispatchType, $resource);
}
