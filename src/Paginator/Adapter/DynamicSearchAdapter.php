<?php

namespace DynamicSearchBundle\Paginator\Adapter;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\OutputChannel\Query\Result\RawResultInterface;
use DynamicSearchBundle\Paginator\AdapterInterface;

class DynamicSearchAdapter implements AdapterInterface
{
    protected ContextDefinitionInterface $contextDefinition;
    protected string $outputChannelName;
    protected ?DocumentNormalizerInterface $documentNormalizer = null;
    protected RawResultInterface $rawResult;
    protected int $itemCountPerPage;
    protected int $currentPageNumber;

    public function __construct(RawResultInterface $rawResult)
    {
        $this->rawResult = $rawResult;
    }

    public function setContextDefinition(ContextDefinitionInterface $contextDefinition): void
    {
        $this->contextDefinition = $contextDefinition;
    }

    public function setOutputChannelName(string $outputChannelName): void
    {
        $this->outputChannelName = $outputChannelName;
    }

    public function setDocumentNormalizer(?DocumentNormalizerInterface $documentNormalizer): void
    {
        $this->documentNormalizer = $documentNormalizer;
    }

    /**
     * @throws \Exception
     */
    public function getItems(int $offset, int $itemCountPerPage): array
    {
        $data = $this->rawResult->getData();

        if (!is_array($data)) {
            return [];
        }

        $data = count($data) > $offset ? array_slice($data, $offset, $itemCountPerPage) : $data;

        // clone raw result and reset data to the requested range of items
        $rawResult = clone $this->rawResult;
        $rawResult->setData($data);

        if ($this->documentNormalizer instanceof DocumentNormalizerInterface) {
            $data = $this->documentNormalizer->normalize($rawResult, $this->contextDefinition, $this->outputChannelName);
        }

        return $data;
    }

    public function setItemCountPerPage(int $itemCountPerPage): void
    {
        $this->itemCountPerPage = $itemCountPerPage;
    }

    public function setCurrentPageNumber(int $currentPageNumber): void
    {
        $this->currentPageNumber = $currentPageNumber;
    }

    public function getCount(): int
    {
        return $this->rawResult->getHitCount();
    }
}
