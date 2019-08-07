<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

interface OutputChannelResultInterface
{
    /**
     * @return string
     */
    public function getContextName();

    /**
     * @return int
     */
    public function getHitCount();

    /**
     * @return OutputChannelAllocatorInterface
     */
    public function getOutputChannelAllocator();

    /**
     * @return array
     */
    public function getFilter();

    /**
     * @return RuntimeQueryProviderInterface
     */
    public function getRuntimeQueryProvider();

    /**
     * @return \ArrayObject
     */
    public function getRuntimeOptions();
}
