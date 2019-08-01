<?php

namespace DynamicSearchBundle\OutputChannel\Allocator;

interface OutputChannelAllocatorInterface
{
    /**
     * @return string
     */
    public function getOutputChannelName();

    /**
     * @return string|null
     */
    public function getParentOutputChannelName();

    /**
     * @return string|null
     */
    public function getSubOutputChannelIdentifier();
}
