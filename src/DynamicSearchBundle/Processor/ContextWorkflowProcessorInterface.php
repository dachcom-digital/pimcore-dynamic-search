<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Exception\RuntimeException;

interface ContextWorkflowProcessorInterface
{
    /**
     * @param array $runtimeOptions
     *
     * @throws RuntimeException
     */
    public function dispatchFullContextCreation(array $runtimeOptions = []);

    /**
     * @param string $contextName
     * @param array  $runtimeOptions
     *
     * @throws RuntimeException
     */
    public function dispatchSingleContextCreation(string $contextName, array $runtimeOptions = []);

    /**
     * @param string $contextName
     * @param array  $runtimeOptions
     *
     * @throws RuntimeException
     */
    public function dispatchInsert(string $contextName, array $runtimeOptions = []);

    /**
     * @param string $contextName
     * @param array  $runtimeOptions
     *
     * @throws RuntimeException
     */
    public function dispatchUpdate(string $contextName, array $runtimeOptions = []);

    /**
     * @param string $contextName
     * @param array  $runtimeOptions
     *
     * @throws RuntimeException
     */
    public function dispatchDeletion(string $contextName, array $runtimeOptions = []);
}
