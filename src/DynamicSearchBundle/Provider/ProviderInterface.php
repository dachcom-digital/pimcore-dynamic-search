<?php

namespace DynamicSearchBundle\Provider;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\ProcessCancelledException;
use DynamicSearchBundle\Exception\ProviderException;

interface ProviderInterface
{
    public function setOptions(array $options): void;

    public function warmUp(ContextDefinitionInterface $contextDefinition): void;

    public function coolDown(ContextDefinitionInterface $contextDefinition): void;

    public function cancelledShutdown(ContextDefinitionInterface $contextDefinition): void;

    public function emergencyShutdown(ContextDefinitionInterface $contextDefinition): void;
}
