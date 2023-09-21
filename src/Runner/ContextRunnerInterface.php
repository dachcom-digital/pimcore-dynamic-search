<?php

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Exception\SilentException;

interface ContextRunnerInterface
{
    /**
     * @throws SilentException
     */
    public function runFullContextCreation(): void;

    /**
     * @throws SilentException
     */
    public function runSingleContextCreation(string $contextName): void;
}
