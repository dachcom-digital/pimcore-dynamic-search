<?php

namespace DynamicSearchBundle\Transformer;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Service\OptionAwareResolverInterface;

interface DataTransformerInterface extends OptionAwareResolverInterface
{
    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger);

    /**
     * @return string
     */
    public function getAlias(): string;

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
     * @return IndexDocument|bool
     */
    public function transformData(ContextDataInterface $contextData, $data);
}