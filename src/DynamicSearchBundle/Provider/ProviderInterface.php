<?php

namespace DynamicSearchBundle\Provider;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\ProcessCancelledException;
use DynamicSearchBundle\Exception\ProviderException;

interface ProviderInterface
{
    /**
     * @param array $options
     */
    public function setOptions(array $options);

    /**
     * @param ContextDefinitionInterface $contextDefinition
     *
     * @throws ProviderException
     * @throws ProcessCancelledException
     */
    public function warmUp(ContextDefinitionInterface $contextDefinition);

    /**
     * @param ContextDefinitionInterface $contextDefinition
     *
     * @throws ProviderException
     * @throws ProcessCancelledException
     */
    public function coolDown(ContextDefinitionInterface $contextDefinition);

    /**
     * @param ContextDefinitionInterface $contextDefinition
     */
    public function cancelledShutdown(ContextDefinitionInterface $contextDefinition);

    /**
     * @param ContextDefinitionInterface $contextDefinition
     */
    public function emergencyShutdown(ContextDefinitionInterface $contextDefinition);
}
