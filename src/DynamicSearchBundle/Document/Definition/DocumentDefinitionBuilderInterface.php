<?php

namespace DynamicSearchBundle\Document\Definition;

interface DocumentDefinitionBuilderInterface
{
    /**
     * @param DocumentDefinitionInterface $definition
     *
     * @return DocumentDefinitionInterface
     */
    public function buildDefinition(DocumentDefinitionInterface $definition);

}