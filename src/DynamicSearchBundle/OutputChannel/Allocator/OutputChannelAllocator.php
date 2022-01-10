<?php

namespace DynamicSearchBundle\OutputChannel\Allocator;

class OutputChannelAllocator implements OutputChannelAllocatorInterface
{
    protected string $outputChannelName;
    protected ?string $parentOutputChannelName;
    protected ?string $subOutputChannelIdentifier;

    public function __construct(
        string $outputChannelName,
        ?string $parentOutputChannelName,
        ?string $subOutputChannelIdentifier
    ) {
        $this->outputChannelName = $outputChannelName;
        $this->parentOutputChannelName = $parentOutputChannelName;
        $this->subOutputChannelIdentifier = $subOutputChannelIdentifier;
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
