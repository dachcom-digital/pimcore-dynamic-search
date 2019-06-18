<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Exception\RuntimeException;

interface ContextWorkflowProcessorInterface
{
    /**
     * @throws RuntimeException
     */
    public function dispatchFullContextLoop();

    /**
     * @param string $contextName
     *
     * @throws RuntimeException
     */
    public function dispatchSingleContextLoop(string $contextName);
}
