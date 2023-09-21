<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Exception\OutputChannelException;
use DynamicSearchBundle\OutputChannel\Result\MultiOutputChannelResultInterface;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelResultInterface;

interface OutputChannelProcessorInterface
{
    /**
     * @throws OutputChannelException
     */
    public function dispatchOutputChannelQuery(string $contextName, string $outputChannelName): OutputChannelResultInterface|MultiOutputChannelResultInterface;
}
