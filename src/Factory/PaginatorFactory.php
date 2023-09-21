<?php

namespace DynamicSearchBundle\Factory;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\OutputChannel\Query\Result\RawResultInterface;
use DynamicSearchBundle\Paginator\AdapterInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class PaginatorFactory implements PaginatorFactoryInterface
{
    public function __construct(protected PaginatorInterface $paginator)
    {
    }

    public function create(
        string $adapterClass,
        int $itemCountPerPage,
        int $currentPageNumber,
        string $outputChannelName,
        RawResultInterface $rawResult,
        ContextDefinitionInterface $contextDefinition,
        ?DocumentNormalizerInterface $documentNormalizer
    ): PaginationInterface {

        /** @var AdapterInterface $adapter */
        $adapter = new $adapterClass($rawResult);
        $adapter->setContextDefinition($contextDefinition);
        $adapter->setOutputChannelName($outputChannelName);
        $adapter->setDocumentNormalizer($documentNormalizer);
        $adapter->setItemCountPerPage($itemCountPerPage);
        $adapter->setCurrentPageNumber($currentPageNumber);

        return $this->paginator->paginate($adapter, $currentPageNumber, $itemCountPerPage);
    }
}
