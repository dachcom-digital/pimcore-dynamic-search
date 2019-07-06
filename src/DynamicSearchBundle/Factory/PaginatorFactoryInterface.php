<?php

namespace DynamicSearchBundle\Factory;

use DynamicSearchBundle\Document\Definition\OutputDocumentDefinitionInterface;
use DynamicSearchBundle\Paginator\Paginator;

interface PaginatorFactoryInterface
{
    /**
     * @param mixed                             $adapterData
     * @param string                            $adapterClass
     * @param string                            $contextName
     * @param string                            $outputChannelName
     * @param OutputDocumentDefinitionInterface $outputDocumentDefinition
     *
     * @return Paginator
     */
    public function create(
        $adapterData,
        string $adapterClass,
        string $contextName,
        string $outputChannelName,
        OutputDocumentDefinitionInterface $outputDocumentDefinition
    );
}