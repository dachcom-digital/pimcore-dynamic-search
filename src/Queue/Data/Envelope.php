<?php

namespace DynamicSearchBundle\Queue\Data;

use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

class Envelope
{
    public function __construct(
        protected string $id,
        protected string $contextName,
        protected string $dispatchType,
        protected array $resourceMetaStack,
        protected array $options,
        protected ?float $creationTime = null
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getContextName(): string
    {
        return $this->contextName;
    }

    public function getDispatchType(): string
    {
        return $this->dispatchType;
    }

    /**
     * @return array<int, ResourceMetaInterface>
     */
    public function getResourceMetaStack(): array
    {
        return $this->resourceMetaStack;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getCreationTime(): ?float
    {
        return $this->creationTime;
    }
}
