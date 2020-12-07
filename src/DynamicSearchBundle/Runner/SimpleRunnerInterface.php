<?php

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Exception\SilentException;

interface SimpleRunnerInterface
{
    /**
     * @param string $contextName
     * @param mixed  $resource
     *
     * @throws SilentException
     */
    public function runInsert(string $contextName, $resource);

    /**
     * @param string $contextName
     * @param mixed  $resource
     *
     * @throws SilentException
     */
    public function runUpdate(string $contextName, $resource);

    /**
     * @param string $contextName
     * @param mixed  $resource
     *
     * @throws SilentException
     */
    public function runDelete(string $contextName, $resource);
}
