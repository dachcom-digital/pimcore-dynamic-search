<?php

namespace DynamicSearchBundle\OutputChannel\Modifier;

use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;

interface OutputChannelModifierFilterInterface
{
    public function dispatchFilter(OutputChannelAllocatorInterface $outputChannelAllocator, array $options);
}
