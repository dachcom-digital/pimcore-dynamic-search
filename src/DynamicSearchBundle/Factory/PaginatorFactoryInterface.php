<?php

namespace DynamicSearchBundle\Factory;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\Paginator\Paginator;

interface PaginatorFactoryInterface
{
    /**
     * @param mixed                            $adapterData
     * @param string                           $adapterClass
     * @param string                           $outputChannelName
     * @param ContextDefinitionInterface             $contextDefinition
     * @param DocumentNormalizerInterface|null $documentNormalizer
     *
     * @return Paginator
     */
    public function create(
        $adapterData,
        string $adapterClass,
        string $outputChannelName,
        ContextDefinitionInterface $contextDefinition,
        ?DocumentNormalizerInterface $documentNormalizer
    );
}
