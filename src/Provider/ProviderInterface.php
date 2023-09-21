<?php

namespace DynamicSearchBundle\Provider;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\ProcessCancelledException;
use DynamicSearchBundle\Exception\ProviderException;

interface ProviderInterface
{
    public function setOptions(array $options): void;

    /**
     * @throws ProviderException
     * @throws ProcessCancelledException
     */
    public function warmUp(ContextDefinitionInterface $contextDefinition): void;

    /**
     * @throws ProviderException
     * @throws ProcessCancelledException
     */
    public function coolDown(ContextDefinitionInterface $contextDefinition): void;

    public function cancelledShutdown(ContextDefinitionInterface $contextDefinition): void;

    public function emergencyShutdown(ContextDefinitionInterface $contextDefinition): void;
}
