<?php

namespace DynamicSearchBundle\Factory;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\OutputChannel\Query\Result\RawResultInterface;
use DynamicSearchBundle\Paginator\PaginatorInterface;

interface PaginatorFactoryInterface
{
    public function create(
        string $adapterClass,
        string $outputChannelName,
        RawResultInterface $rawResult,
        ContextDefinitionInterface $contextDefinition,
        ?DocumentNormalizerInterface $documentNormalizer
    ): PaginatorInterface;
}
