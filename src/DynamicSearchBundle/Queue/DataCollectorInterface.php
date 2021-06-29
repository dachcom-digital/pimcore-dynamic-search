<?php

namespace DynamicSearchBundle\Queue;

interface DataCollectorInterface
{
    public function addToGlobalQueue(string $dispatchType, $resource, array $options = []): void;

    public function addToContextQueue(string $contextName, string $dispatchType, $resource, array $options = []): void;
}
