<?php

namespace DynamicSearchBundle\Queue\Data;

use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

class Envelope
{
    protected string $id;
    protected string $contextName;
    protected string $dispatchType;
    protected array $resourceMetaStack;
    protected array $options;

    public function __construct(string $id, string $contextName, string $dispatchType, array $resourceMetaStack, array $options)
    {
        $this->id = $id;
        $this->contextName = $contextName;
        $this->dispatchType = $dispatchType;
        $this->resourceMetaStack = $resourceMetaStack;
        $this->options = $options;
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
     * @return ResourceMetaInterface[]
     */
    public function getResourceMetaStack(): array
    {
        return $this->resourceMetaStack;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
