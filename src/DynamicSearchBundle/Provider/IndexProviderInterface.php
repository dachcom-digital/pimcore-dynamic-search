<?php

namespace DynamicSearchBundle\Provider;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Service\OptionAwareResolverInterface;

interface IndexProviderInterface extends OptionAwareResolverInterface
{
    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger);

    /**
     * @param ContextDataInterface $contextData
     */
    public function warmUp(ContextDataInterface $contextData);

    /**
     * @param ContextDataInterface $contextData
     */
    public function coolDown(ContextDataInterface $contextData);

    public function executeInsert(ContextDataInterface $contextData, IndexDocument $indexDocument);

    public function executeUpdate(ContextDataInterface $contextData, IndexDocument $indexDocument);

    public function executeDelete(ContextDataInterface $contextData, IndexDocument $indexDocument);
}