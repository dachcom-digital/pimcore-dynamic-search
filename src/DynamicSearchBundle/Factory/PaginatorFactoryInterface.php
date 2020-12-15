<?php

namespace DynamicSearchBundle\Factory;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\OutputChannel\Query\Result\RawResultInterface;
use DynamicSearchBundle\Paginator\Paginator;

interface PaginatorFactoryInterface
{
    /**
     * @param string                           $adapterClass
     * @param string                           $outputChannelName
     * @param RawResultInterface               $rawResult
     * @param ContextDefinitionInterface       $contextDefinition
     * @param DocumentNormalizerInterface|null $documentNormalizer
     *
     * @return Paginator
     */
    public function create(
        string $adapterClass,
        string $outputChannelName,
        RawResultInterface $rawResult,
        ContextDefinitionInterface $contextDefinition,
        ?DocumentNormalizerInterface $documentNormalizer
    );
}
