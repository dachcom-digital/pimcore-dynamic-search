<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Exception\DataTransformerException;
use DynamicSearchBundle\Transformer\DataTransformerInterface;

interface DataTransformerManagerInterface
{
    /**
     * @param string               $dataProvider
     * @param ContextDataInterface $contextData
     * @param bool|IndexDocument   $data
     *
     * @returns DataTransformerInterface
     * @throws DataTransformerException
     */
    public function getDataTransformer(string $dataProvider, ContextDataInterface $contextData, $data);
}
