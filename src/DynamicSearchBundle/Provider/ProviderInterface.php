<?php

namespace DynamicSearchBundle\Provider;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ProcessCancelledException;
use DynamicSearchBundle\Exception\ProviderException;

interface ProviderInterface
{
    /**
     * @param array $options
     */
    public function setOptions(array $options);

    /**
     * @param ContextDataInterface $contextData
     *
     * @throws ProviderException
     * @throws ProcessCancelledException
     */
    public function warmUp(ContextDataInterface $contextData);

    /**
     * @param ContextDataInterface $contextData
     *
     * @throws ProviderException
     * @throws ProcessCancelledException
     */
    public function coolDown(ContextDataInterface $contextData);

    /**
     * @param ContextDataInterface $contextData
     */
    public function cancelledShutdown(ContextDataInterface $contextData);

    /**
     * @param ContextDataInterface $contextData
     */
    public function emergencyShutdown(ContextDataInterface $contextData);
}