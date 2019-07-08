<?php

namespace DynamicSearchBundle\Factory;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\Paginator\AdapterInterface;
use DynamicSearchBundle\Paginator\PaginatorInterface;

class PaginatorFactory implements PaginatorFactoryInterface
{

    /**
     * @var string
     */
    protected $paginatorClass;

    /**
     * @param string $paginatorClass
     */
    public function __construct(string $paginatorClass)
    {
        $this->paginatorClass = $paginatorClass;
    }

    /**
     * {@inheritDoc}
     */
    public function create(
        $adapterData,
        string $adapterClass,
        string $outputChannelName,
        ContextDataInterface $contextData,
        ?DocumentNormalizerInterface $documentNormalizer
    ) {
        $paginatorClassName = $this->paginatorClass;

        /** @var AdapterInterface $adapter */
        $adapter = new $adapterClass($adapterData);
        $adapter->setContext($contextData);
        $adapter->setOutputChannelName($outputChannelName);
        $adapter->setDocumentNormalizer($documentNormalizer);

        /** @var PaginatorInterface $paginator */
        $paginator = new $paginatorClassName($adapter);

        return $paginator;

    }
}