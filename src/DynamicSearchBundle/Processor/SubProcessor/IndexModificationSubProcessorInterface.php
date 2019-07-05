<?php

namespace DynamicSearchBundle\Processor\SubProcessor;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\RuntimeException;

interface IndexModificationSubProcessorInterface
{
    /**
     * @param ContextDataInterface $contextData
     * @param mixed                $resource
     *
     * @throws RuntimeException
     */
    public function dispatch(ContextDataInterface $contextData, $resource);
}
