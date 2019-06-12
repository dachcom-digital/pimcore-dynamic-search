<?php

namespace DynamicSearchBundle\Provider;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Service\OptionAwareResolverInterface;

interface DataProviderInterface extends OptionAwareResolverInterface
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

    /**
     * @param ContextDataInterface $contextData
     */
    public function execute(ContextDataInterface $contextData);
}