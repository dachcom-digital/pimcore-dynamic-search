<?php

namespace DynamicSearchBundle\Queue;

interface DataCollectorInterface
{
    /**
     * @param string $contextName
     * @param string $dispatchType
     * @param mixed  $resource
     * @param array  $options
     *
     * @return mixed
     */
    public function addToQueue(string $contextName, string $dispatchType, $resource, array $options = []);
}