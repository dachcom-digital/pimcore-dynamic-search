<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Document\IndexDocument;

interface TransformerWorkflowProcessorInterface
{
    /**
     * @param ContextDataInterface $contextData
     * @param mixed                $data
     *
     * @return null|IndexDocument
     */
    public function dispatchIndexDocumentTransform(ContextDataInterface $contextData, $data);
}
