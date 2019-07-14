<?php

namespace DynamicSearchBundle\Runner;

interface ContextRunnerInterface
{
    public function runFullContextCreation();

    public function runSingleContextCreation(string $contextName);
}
