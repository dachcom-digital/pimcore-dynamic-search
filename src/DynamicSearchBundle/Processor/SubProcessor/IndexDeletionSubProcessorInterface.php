<?php

namespace DynamicSearchBundle\Processor\SubProcessor;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Document\IndexDocument;

interface IndexDeletionSubProcessorInterface
{
    /**
     * @param ContextDataInterface $contextData
     * @param IndexDocument        $indexDocument
     */
    public function dispatch(ContextDataInterface $contextData, IndexDocument $indexDocument);
}
