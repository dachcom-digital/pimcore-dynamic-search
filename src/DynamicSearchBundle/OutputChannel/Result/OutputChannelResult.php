<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsProviderInterface;

class OutputChannelResult implements OutputChannelResultInterface
{
    /**
     * @var string
     */
    protected $contextName;

    /**
     * @var string
     */
    protected $outputChannelName;

    /**
     * @var array
     */
    protected $filter;

    /**
     * @var RuntimeOptionsProviderInterface
     */
    protected $runtimeOptionsProvider;

    /**
     * @param string                          $contextName
     * @param string                          $outputChannelName
     * @param array                           $filter
     * @param RuntimeOptionsProviderInterface $runtimeOptionsProvider
     */
    public function __construct(
        string $contextName,
        string $outputChannelName,
        array $filter,
        RuntimeOptionsProviderInterface $runtimeOptionsProvider
    ) {
        $this->contextName = $contextName;
        $this->outputChannelName = $outputChannelName;
        $this->filter = $filter;
        $this->runtimeOptionsProvider = $runtimeOptionsProvider;
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
    public function getOutputChannelName()
    {
        return $this->outputChannelName;
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
    public function getRuntimeOptionsProvider()
    {
        return $this->runtimeOptionsProvider;
    }
}
