<?php

namespace DynamicSearchBundle\OutputChannel\Allocator;

class OutputChannelAllocator implements OutputChannelAllocatorInterface
{
    public function __construct(
        protected string $outputChannelName,
        protected ?string $parentOutputChannelName,
        protected ?string $subOutputChannelIdentifier
    ) {
    }

    public function getOutputChannelName(): string
    {
        return $this->outputChannelName;
    }

    public function getParentOutputChannelName(): ?string
    {
        return $this->parentOutputChannelName;
    }

    public function getSubOutputChannelIdentifier(): ?string
    {
        return $this->subOutputChannelIdentifier;
    }
}
