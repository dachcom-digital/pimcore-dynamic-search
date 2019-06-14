<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Exception\RuntimeException;

interface WorkflowProcessorInterface
{
    /**
     * @throws RuntimeException
     */
    public function performFullContextLoop();

    /**
     * @param string $contextName
     *
     * @throws RuntimeException
     */
    public function performSingleContextLoop(string $contextName);
}
