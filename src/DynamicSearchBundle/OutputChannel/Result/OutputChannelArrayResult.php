<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsProviderInterface;

class OutputChannelArrayResult implements OutputChannelArrayResultInterface
{
    protected $contextName;

    protected $outputChannelName;

    protected $runtimeOptionsProvider;

    protected $result;

    /**
     * @param string                          $contextName
     * @param string                          $outputChannelName
     * @param array                           $result
     * @param RuntimeOptionsProviderInterface $runtimeOptionsProvider
     */
    public function __construct(
        string $contextName,
        string $outputChannelName,
        RuntimeOptionsProviderInterface $runtimeOptionsProvider,
        array $result
    ) {
        $this->contextName = $contextName;
        $this->outputChannelName = $outputChannelName;
        $this->runtimeOptionsProvider = $runtimeOptionsProvider;
        $this->result = $result;
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
    public function getResult()
    {
        return $this->result;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuntimeOptionsProvider()
    {
        return $this->runtimeOptionsProvider;
    }
}
