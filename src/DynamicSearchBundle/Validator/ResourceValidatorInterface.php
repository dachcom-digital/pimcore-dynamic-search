<?php

namespace DynamicSearchBundle\Validator;

interface ResourceValidatorInterface
{
    /**
     * @param string $contextName
     * @param string $dispatchType
     * @param        $resource
     *
     * @return mixed resource
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
