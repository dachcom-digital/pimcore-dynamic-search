<?php

namespace DynamicSearchBundle\Event;

use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use Symfony\Contracts\EventDispatcher\Event;

class NewDataEvent extends Event
{
    protected string $contextDispatchType;
    protected string $contextName;
    protected mixed $data;
    protected string $providerBehaviour;
    protected ?ResourceMetaInterface $resourceMeta;

    public function __construct(string $contextDispatchType, string $contextName, mixed $data, string $providerBehaviour, ?ResourceMetaInterface $resourceMeta)
    {
        $this->contextDispatchType = $contextDispatchType;
        $this->contextName = $contextName;
        $this->data = $data;
        $this->providerBehaviour = $providerBehaviour;
        $this->resourceMeta = $resourceMeta;
    }

    public function getContextDispatchType(): string
    {
        return $this->contextDispatchType;
    }

    public function getContextName(): string
    {
        return $this->contextName;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getProviderBehaviour(): string
    {
        return $this->providerBehaviour;
    }

    public function getResourceMeta(): ?ResourceMetaInterface
    {
        return $this->resourceMeta;
    }
}
