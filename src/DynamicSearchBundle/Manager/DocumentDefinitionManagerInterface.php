<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;

interface DocumentDefinitionManagerInterface
{
    /**
     * @param ContextDataInterface $contextData
     *
     * @return DocumentDefinitionBuilderInterface|null
     */
    public function getDocumentDefinitionBuilder(ContextDataInterface $contextData);
}
