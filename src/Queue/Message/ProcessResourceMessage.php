<?php

namespace DynamicSearchBundle\Queue\Message;

use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

readonly class ProcessResourceMessage
{
    public function __construct(
        public string $contextName,
        public string $dispatchType,
        public ResourceMetaInterface $resourceMeta
    )
    {}
}
