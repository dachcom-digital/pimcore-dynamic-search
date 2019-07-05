<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Document\IndexDocumentDefinitionBuilderInterface;

interface IndexDocumentDefinitionManagerInterface
{
    /**
     * @param ContextDataInterface $contextData
     *
     * @return IndexDocumentDefinitionBuilderInterface|null
     */
    public function getIndexDocumentDefinitionBuilder(ContextDataInterface $contextData);
}
