<?php

namespace DynamicSearchBundle\OutputChannel\Modifier;

use DynamicSearchBundle\Event\OutputModifierEvent;
use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;

interface OutputChannelModifierActionInterface
{
    public function dispatchAction(string $action, OutputChannelAllocatorInterface $outputChannelAllocator, OutputModifierEvent $event): OutputModifierEvent;
}
