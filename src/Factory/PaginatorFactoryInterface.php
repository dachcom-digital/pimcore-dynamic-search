<?php

namespace DynamicSearchBundle\Factory;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\OutputChannel\Query\Result\RawResultInterface;

interface PaginatorFactoryInterface
{
    public function create(
        string $adapterClass,
        int $itemCountPerPage,
        int $currentPageNumber,
        string $outputChannelName,
        RawResultInterface $rawResult,
        ContextDefinitionInterface $contextDefinition,
        ?DocumentNormalizerInterface $documentNormalizer
    );
}
