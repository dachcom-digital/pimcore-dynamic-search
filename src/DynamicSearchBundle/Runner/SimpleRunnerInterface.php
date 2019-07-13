<?php

namespace DynamicSearchBundle\Runner;

interface SimpleRunnerInterface
{
    /**
     * @param string $contextName
     * @param mixed  $resource
     */
    public function runInsert(string $contextName, $resource);

    /**
     * @param string $contextName
     * @param mixed  $resource
     */
    public function runUpdate(string $contextName, $resource);

    /**
     * @param string $contextName
     * @param mixed  $resource
     */
    public function runDelete(string $contextName, $resource);
}