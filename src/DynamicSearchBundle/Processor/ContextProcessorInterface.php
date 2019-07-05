<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Exception\RuntimeException;

interface ContextProcessorInterface
{
    /**
     * @param array $runtimeValues
     *
     * @throws RuntimeException
     */
    public function dispatchFullContextCreation(array $runtimeValues = []);

    /**
     * @param string $contextName
     * @param array  $runtimeValues
     *
     * @throws RuntimeException
     */
    public function dispatchSingleContextCreation(string $contextName, array $runtimeValues = []);

    /**
     * @param string $contextName
     * @param string $dispatchType
     * @param mixed  $resource
     * @param array  $runtimeValues
     */
    public function dispatchContextModification(string $contextName, string $dispatchType, $resource, array $runtimeValues = []);

    /**
     * @param string        $contextName
     * @param string        $dispatchType
     * @param array|mixed[] $resources
     * @param array         $runtimeValues
     *
     * @return mixed
     */
    public function dispatchContextModificationStack(string $contextName, string $dispatchType, array $resources, array $runtimeValues = []);

}
