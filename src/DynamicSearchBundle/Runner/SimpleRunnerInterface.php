<?php

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Exception\SilentException;

interface SimpleRunnerInterface
{
    public function runInsert(string $contextName, $resource): void;

    public function runUpdate(string $contextName, $resource): void;

    public function runDelete(string $contextName, $resource): void;
}
