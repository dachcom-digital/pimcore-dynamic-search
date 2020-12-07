<?php

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Exception\SilentException;

interface ContextRunnerInterface
{
    /**
     * @return void
     *
     * @throws SilentException
     */
    public function runFullContextCreation();

    /**
     * @param string $contextName
     *
     * @return void
     *
     * @throws SilentException
     */
    public function runSingleContextCreation(string $contextName);
}
