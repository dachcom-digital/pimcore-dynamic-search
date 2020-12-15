<?php

namespace DynamicSearchBundle\Paginator\Adapter;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\OutputChannel\Query\Result\RawResultInterface;
use DynamicSearchBundle\Paginator\AdapterInterface;

class DynamicSearchAdapter implements AdapterInterface
{
    /**
     * @var ContextDefinitionInterface
     */
    protected $contextDefinition;

    /**
     * @var string
     */
    protected $outputChannelName;

    /**
     * @var DocumentNormalizerInterface
     */
    protected $documentNormalizer;

    /**
     * @var RawResultInterface
     */
    protected $rawResult;

    /**
     * @param RawResultInterface $rawResult
     */
    public function __construct(RawResultInterface $rawResult)
    {
        $this->rawResult = $rawResult;
    }

    /**
     * {@inheritdoc}
     */
    public function setContextDefinition(ContextDefinitionInterface $contextDefinition)
    {
        $this->contextDefinition = $contextDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function setOutputChannelName(string $outputChannelName)
    {
        $this->outputChannelName = $outputChannelName;
    }

    /**
     * {@inheritdoc}
     */
    public function setDocumentNormalizer(?DocumentNormalizerInterface $documentNormalizer)
    {
        $this->documentNormalizer = $documentNormalizer;
    }

    /**
     * @param int $offset           Page offset
     * @param int $itemCountPerPage Number of items per page
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getItems($offset, $itemCountPerPage)
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

    /**
     * Returns the total number of rows in the array.
     *
     * @return int
     */
    public function count()
    {
        return $this->rawResult->getHitCount();
    }
}
