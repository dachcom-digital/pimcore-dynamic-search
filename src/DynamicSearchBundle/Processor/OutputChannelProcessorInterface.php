<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Exception\OutputChannelException;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelResultInterface;

interface OutputChannelProcessorInterface
{
    public function dispatchOutputChannelQuery(string $contextName, string $outputChannelName);
}
