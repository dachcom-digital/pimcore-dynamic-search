<?php

namespace DynamicSearchBundle\Transformer;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Logger\LoggerInterface;

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
     * @return array
     */
    public function transformData(ContextDataInterface $contextData, $resource): array;

}