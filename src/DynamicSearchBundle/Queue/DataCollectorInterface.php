<?php

namespace DynamicSearchBundle\Queue;

interface DataCollectorInterface
{
    public function addToGlobalQueue(string $dispatchType, mixed $resource, array $options = []): void;

    public function addToContextQueue(string $contextName, string $dispatchType, mixed $resource, array $options = []): void;
}
