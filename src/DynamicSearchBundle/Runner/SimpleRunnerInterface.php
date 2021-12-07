<?php

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Exception\SilentException;

interface SimpleRunnerInterface
{
    /**
     * @throws SilentException
     */
    public function runInsert(string $contextName, mixed $resource): void;

    /**
     * @throws SilentException
     */
    public function runUpdate(string $contextName, mixed $resource): void;

    /**
     * @throws SilentException
     */
    public function runDelete(string $contextName, mixed $resource): void;
}
