<?php

namespace DynamicSearchBundle\Runner;

interface ContextRunnerInterface
{
    public function runFullContextCreation(array $runtimeValues = []);

    public function runSingleContextCreation(string $contextName, array $runtimeValues = []);

}