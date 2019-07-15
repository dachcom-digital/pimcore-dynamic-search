<?php

namespace DynamicSearchBundle\Queue;

interface DataCollectorInterface
{
    /**
     * @param string $dispatchType
     * @param mixed  $resource
     * @param array  $options
     */
    public function addToGlobalQueue(string $dispatchType, $resource, array $options = []);

    /**
     * @param string $contextName
     * @param string $dispatchType
     * @param mixed  $resource
     * @param array  $options
     */
    public function addToContextQueue(string $contextName, string $dispatchType, $resource, array $options = []);
}
