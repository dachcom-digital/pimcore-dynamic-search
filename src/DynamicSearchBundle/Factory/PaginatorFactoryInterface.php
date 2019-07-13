<?php

namespace DynamicSearchBundle\Factory;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\Paginator\Paginator;

interface PaginatorFactoryInterface
{
    /**
     * @param mixed                            $adapterData
     * @param string                           $adapterClass
     * @param string                           $outputChannelName
     * @param ContextDataInterface             $contextData
     * @param DocumentNormalizerInterface|null $documentNormalizer
     *
     * @return Paginator
     */
    public function create(
        $adapterData,
        string $adapterClass,
        string $outputChannelName,
        ContextDataInterface $contextData,
        ?DocumentNormalizerInterface $documentNormalizer
    );
}
