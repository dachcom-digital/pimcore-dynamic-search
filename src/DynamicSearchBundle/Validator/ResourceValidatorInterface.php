<?php

namespace DynamicSearchBundle\Validator;

interface ResourceValidatorInterface
{
    /**
     * @param string                $contextName
     * @param string                $dispatchType
     * @param mixed                 $resource
     *
     * @return mixed
     */
    public function validateUntrustedResource(string $contextName, string $dispatchType, $resource);
}
