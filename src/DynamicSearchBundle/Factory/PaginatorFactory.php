<?php

namespace DynamicSearchBundle\Factory;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\OutputChannel\Query\Result\RawResultInterface;
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
     * {@inheritdoc}
     */
    public function create(
        string $adapterClass,
        string $outputChannelName,
        RawResultInterface $rawResult,
        ContextDefinitionInterface $contextDefinition,
        ?DocumentNormalizerInterface $documentNormalizer
    ) {
        $paginatorClassName = $this->paginatorClass;

        /** @var AdapterInterface $adapter */
        $adapter = new $adapterClass($rawResult);
        $adapter->setContextDefinition($contextDefinition);
        $adapter->setOutputChannelName($outputChannelName);
        $adapter->setDocumentNormalizer($documentNormalizer);

        /** @var PaginatorInterface $paginator */
        $paginator = new $paginatorClassName($adapter);

        return $paginator;
    }
}
