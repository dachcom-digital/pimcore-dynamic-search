<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

interface OutputChannelResultInterface
{
    public function getContextName(): string;

    public function getHitCount(): int;

    public function getOutputChannelAllocator(): OutputChannelAllocatorInterface;

    public function getFilter(): array;

    public function getRuntimeQueryProvider(): RuntimeQueryProviderInterface;

    public function getRuntimeOptions(): \ArrayObject;
}
