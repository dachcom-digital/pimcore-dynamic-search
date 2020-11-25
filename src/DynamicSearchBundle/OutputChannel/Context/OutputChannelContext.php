<?php

namespace DynamicSearchBundle\OutputChannel\Context;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

class OutputChannelContext implements OutputChannelContextInterface
{
    /**
     * @var ContextDefinitionInterface
     */
    protected $contextDefinition;

    /**
     * @var RuntimeQueryProviderInterface
     */
    protected $runtimeQueryProvider;

    /**
     * @var \ArrayObject
     */
    protected $runtimeOptions;

    /**
     * @var array
     */
    protected $indexProviderOptions;

    /**
     * @var OutputChannelAllocatorInterface
     */
    protected $outputChannelAllocator;

    /**
     * @var string
     */
    protected $outputChannelServiceName;

    /**
     * @param ContextDefinitionInterface $contextDefinition
     */
    public function setContextDefinition(ContextDefinitionInterface $contextDefinition)
    {
        $this->contextDefinition = $contextDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function getContextDefinition()
    {
        return $this->contextDefinition;
    }

    /**
     * @param RuntimeQueryProviderInterface $runtimeQueryProvider
     */
    public function setRuntimeQueryProvider(RuntimeQueryProviderInterface $runtimeQueryProvider)
    {
        $this->runtimeQueryProvider = $runtimeQueryProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuntimeQueryProvider()
    {
        return $this->runtimeQueryProvider;
    }

    /**
     * @param \ArrayObject $runtimeOptions
     */
    public function setRuntimeOptions(\ArrayObject $runtimeOptions)
    {
        $this->runtimeOptions = $runtimeOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuntimeOptions()
    {
        return $this->runtimeOptions;
    }

    /**
     * @param array $indexProviderOptions
     */
    public function setIndexProviderOptions(array $indexProviderOptions)
    {
        $this->indexProviderOptions = $indexProviderOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexProviderOptions()
    {
        return $this->indexProviderOptions;
    }

    /**
     * @param OutputChannelAllocatorInterface $outputChannelAllocator
     */
    public function setOutputChannelAllocator(OutputChannelAllocatorInterface $outputChannelAllocator)
    {
        $this->outputChannelAllocator = $outputChannelAllocator;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelAllocator()
    {
        return $this->outputChannelAllocator;
    }

    /**
     * @param string $outputChannelServiceName
     */
    public function setOutputChannelServiceName(string $outputChannelServiceName)
    {
        $this->outputChannelServiceName = $outputChannelServiceName;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelServiceName()
    {
        return $this->outputChannelServiceName;
    }
}
