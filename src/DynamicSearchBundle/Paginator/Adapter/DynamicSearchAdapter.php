<?php

namespace DynamicSearchBundle\Paginator\Adapter;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
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
     * @var array
     */
    protected $array = null;

    /**
     * @var int
     */
    protected $count = null;

    /**
     * @param array $data
     */
    public function __construct($data)
    {
        $this->array = $data;
        $this->count = count($this->array);
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
        $data = array_slice($this->array, $offset, $itemCountPerPage);

        if ($this->documentNormalizer instanceof DocumentNormalizerInterface) {
            $data = $this->documentNormalizer->normalize($this->contextDefinition, $this->outputChannelName, $data);
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
        return $this->count;
    }
}
