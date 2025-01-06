<?php

namespace DynamicSearchBundle\OutputChannel\Context;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

interface OutputChannelContextInterface
{
    public function getContextDefinition(): ContextDefinitionInterface;

    public function getRuntimeQueryProvider(): RuntimeQueryProviderInterface;

    public function getRuntimeOptions(): \ArrayObject;

    public function getIndexProviderOptions(): array;

    public function getOutputChannelAllocator(): OutputChannelAllocatorInterface;

    public function getOutputChannelServiceName(): string;
}
