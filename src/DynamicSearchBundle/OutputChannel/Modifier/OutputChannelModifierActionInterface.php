<?php

namespace DynamicSearchBundle\OutputChannel\Modifier;

use DynamicSearchBundle\Event\OutputModifierEvent;
use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;

interface OutputChannelModifierActionInterface
{
    /**
     * @param string                          $action
     * @param OutputChannelAllocatorInterface $outputChannelAllocator
     * @param OutputModifierEvent             $event
     *
     * @return OutputModifierEvent
     */
    public function dispatchAction(string $action, OutputChannelAllocatorInterface $outputChannelAllocator, OutputModifierEvent $event): OutputModifierEvent;
}
