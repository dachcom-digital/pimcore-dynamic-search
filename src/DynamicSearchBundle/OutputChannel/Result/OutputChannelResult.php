<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

class OutputChannelResult implements OutputChannelResultInterface
{
    /**
     * @var string
     */
    protected $contextName;

    /**
     * @var OutputChannelAllocatorInterface
     */
    protected $outputChannelAllocator;

    /**
     * @var array
     */
    protected $filter;

    /**
     * @var \ArrayObject
     */
    protected $runtimeOptions;

    /**
     * @var RuntimeQueryProviderInterface
     */
    protected $runtimeQueryProvider;

    /**
     * @param string                          $contextName
     * @param OutputChannelAllocatorInterface $outputChannelAllocator
     * @param array                           $filter
     * @param \ArrayObject                    $runtimeOptions
     * @param RuntimeQueryProviderInterface   $runtimeQueryProvider
     */
    public function __construct(
        string $contextName,
        OutputChannelAllocatorInterface $outputChannelAllocator,
        array $filter,
        \ArrayObject $runtimeOptions,
        RuntimeQueryProviderInterface $runtimeQueryProvider
    ) {
        $this->contextName = $contextName;
        $this->outputChannelAllocator = $outputChannelAllocator;
        $this->filter = $filter;
        $this->runtimeOptions = $runtimeOptions;
        $this->runtimeQueryProvider = $runtimeQueryProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getContextName()
    {
        return $this->contextName;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelAllocator()
    {
        return $this->outputChannelAllocator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuntimeQueryProvider()
    {
        return $this->runtimeQueryProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuntimeOptions()
    {
        return $this->runtimeOptions;
    }
}
