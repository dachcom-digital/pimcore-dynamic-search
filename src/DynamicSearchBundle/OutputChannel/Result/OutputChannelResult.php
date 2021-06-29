<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

class OutputChannelResult implements OutputChannelResultInterface
{
    protected string $contextName;
    protected int $hitCount;
    protected OutputChannelAllocatorInterface $outputChannelAllocator;
    protected array $filter;
    protected \ArrayObject $runtimeOptions;
    protected RuntimeQueryProviderInterface $runtimeQueryProvider;

    public function __construct(
        string $contextName,
        int $hitCount,
        OutputChannelAllocatorInterface $outputChannelAllocator,
        array $filter,
        \ArrayObject $runtimeOptions,
        RuntimeQueryProviderInterface $runtimeQueryProvider
    ) {
        $this->contextName = $contextName;
        $this->hitCount = $hitCount;
        $this->outputChannelAllocator = $outputChannelAllocator;
        $this->filter = $filter;
        $this->runtimeOptions = $runtimeOptions;
        $this->runtimeQueryProvider = $runtimeQueryProvider;
    }

    public function getContextName(): string
    {
        return $this->contextName;
    }

    public function getHitCount(): int
    {
        return $this->hitCount;
    }

    public function getOutputChannelAllocator(): OutputChannelAllocatorInterface
    {
        return $this->outputChannelAllocator;
    }

    public function getFilter(): array
    {
        return $this->filter;
    }

    public function getRuntimeQueryProvider(): RuntimeQueryProviderInterface
    {
        return $this->runtimeQueryProvider;
    }

    public function getRuntimeOptions(): \ArrayObject
    {
        return $this->runtimeOptions;
    }
}
