<?php

namespace DynamicSearchBundle\Transformer;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Transformer\Container\DocumentContainerInterface;

interface DocumentTransformerInterface
{
    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void;

    /**
     * @param mixed $resource
     *
     * @return bool
     */
    public function isApplicable($resource): bool;

    /**
     * @param ContextDataInterface $contextData
     * @param mixed                $resource
     *
     * @return DocumentContainerInterface|null
     */
    public function transformData(ContextDataInterface $contextData, $resource): ?DocumentContainerInterface;

}