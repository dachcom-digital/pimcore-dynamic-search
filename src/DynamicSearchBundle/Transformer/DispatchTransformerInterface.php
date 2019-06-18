<?php

namespace DynamicSearchBundle\Transformer;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Transformer\Container\DataContainerInterface;

interface DispatchTransformerInterface
{
    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void;

    /**
     * @param mixed $data
     *
     * @return bool
     */
    public function isApplicable($data): bool;

    /**
     * @param ContextDataInterface $contextData
     * @param mixed                $data
     *
     * @return DataContainerInterface|null
     */
    public function transformData(ContextDataInterface $contextData, $data): ?DataContainerInterface;

}