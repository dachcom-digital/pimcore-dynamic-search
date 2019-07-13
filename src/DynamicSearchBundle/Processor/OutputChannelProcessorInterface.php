<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Exception\OutputChannelException;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelResultInterface;

interface OutputChannelProcessorInterface
{
    /**
     * @param string $contextName
     * @param string $outputChannelName
     * @param array  $options
     *
     * @return OutputChannelResultInterface
     *
     * @throws OutputChannelException
     */
    public function dispatchOutputChannelQuery(string $contextName, string $outputChannelName, array $options = []): OutputChannelResultInterface;
}
