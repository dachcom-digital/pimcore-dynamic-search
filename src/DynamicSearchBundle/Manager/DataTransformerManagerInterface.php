<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Exception\DataTransformerException;

interface DataTransformerManagerInterface
{
    /**
     * @param string               $dataProvider
     * @param ContextDataInterface $contextData
     * @param bool|IndexDocument   $data
     *
     * @throws DataTransformerException
     */
    public function execute(string $dataProvider, ContextDataInterface $contextData, $data);
}
