<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Exception\ProviderException;

interface WorkflowProcessorInterface
{
    /**
     * @throws ProviderException
     */
    public function performFullContextLoop();

    /**
     * @param string $contextName
     *
     * @throws ProviderException
     */
    public function performSingleContextLoop(string $contextName);
}
