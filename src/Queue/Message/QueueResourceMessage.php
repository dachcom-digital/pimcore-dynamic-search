<?php

namespace DynamicSearchBundle\Queue\Message;

readonly class QueueResourceMessage
{
    public function __construct(
        public string $contextName,
        public string $dispatchType,
        public string $resourceType,
        public mixed $resource,
        public array $options
    )
    {}
}
