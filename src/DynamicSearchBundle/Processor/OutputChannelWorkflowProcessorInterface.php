<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\OutputChannel\OutputChannelResultInterface;

interface OutputChannelWorkflowProcessorInterface
{
    /**
     * @param string $contextName
     * @param string $outputChannelName
     * @param array  $options
     *
     * @return OutputChannelResultInterface
     */
    public function dispatchOutputChannelQuery(string $contextName, string $outputChannelName, array $options): OutputChannelResultInterface;
}
