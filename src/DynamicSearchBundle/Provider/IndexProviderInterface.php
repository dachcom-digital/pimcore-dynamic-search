<?php

namespace DynamicSearchBundle\Provider;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Exception\ProcessCancelledException;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Service\OptionAwareResolverInterface;

interface IndexProviderInterface extends OptionAwareResolverInterface
{
    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger);

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
     *
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

    /**
     * @param ContextDataInterface $contextData
     * @param IndexDocument        $indexDocument
     *
     * @throws ProviderException
     * @throws ProcessCancelledException
     */
    public function executeInsert(ContextDataInterface $contextData, IndexDocument $indexDocument);

    public function executeUpdate(ContextDataInterface $contextData, IndexDocument $indexDocument);

    public function executeDelete(ContextDataInterface $contextData, IndexDocument $indexDocument);
}