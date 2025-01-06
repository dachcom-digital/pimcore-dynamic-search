<?php

namespace DynamicSearchBundle\OutputChannel\Allocator;

interface OutputChannelAllocatorInterface
{
    public function getOutputChannelName(): string;

    public function getParentOutputChannelName(): ?string;

    public function getSubOutputChannelIdentifier(): ?string;
}
