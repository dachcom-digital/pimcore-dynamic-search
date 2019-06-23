<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Exception\OutputChannelException;
use DynamicSearchBundle\OutputChannel\OutputChannelResultInterface;

interface OutputChannelWorkflowProcessorInterface
{
    /**
     * @param string $contextName
     * @param string $outputChannelName
     * @param array  $options
     *
     * @return OutputChannelResultInterface
     * @throws OutputChannelException
     */
    public function dispatchOutputChannelQuery(string $contextName, string $outputChannelName, array $options): OutputChannelResultInterface;
}
