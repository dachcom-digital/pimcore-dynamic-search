<?php

namespace DynamicSearchBundle\OutputChannel\Modifier;

use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;

interface OutputChannelModifierFilterInterface
{
    /**
     * @param OutputChannelAllocatorInterface $outputChannelAllocator
     * @param array                           $options
     *
     */
    public function dispatchFilter(OutputChannelAllocatorInterface $outputChannelAllocator, array $options);
}
