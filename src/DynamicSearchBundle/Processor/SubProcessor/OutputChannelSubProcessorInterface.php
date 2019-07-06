<?php

namespace DynamicSearchBundle\Processor\SubProcessor;

use DynamicSearchBundle\Exception\OutputChannelException;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelResultInterface;

interface OutputChannelSubProcessorInterface
{
    /**
     * @param string $contextName
     * @param string $outputChannelName
     * @param array  $options
     *
     * @return OutputChannelResultInterface
     * @throws OutputChannelException
     */
    public function dispatchOutputChannelQuery(string $contextName, string $outputChannelName, array $options = []): OutputChannelResultInterface;
}
