<?php

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Exception\SilentException;

interface ContextRunnerInterface
{
    public function runFullContextCreation(): void;

    public function runSingleContextCreation(string $contextName): void;
}
