<?php

namespace DynamicSearchBundle\Document;

use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Resource\Container\IndexFieldContainerInterface;
use DynamicSearchBundle\Resource\Container\OptionFieldContainerInterface;

class IndexDocument
{
    /**
     * @var ResourceMetaInterface
     */
    protected $resourceMeta;

    /**
     * @var array
     */
    protected $documentConfiguration;

    /**
     * @var array
     */
    protected $optionFields;

    /**
     * @var array
     */
    protected $indexFields;

    /**
     * @param ResourceMetaInterface $resourceMeta
     * @param array                 $documentConfiguration
     */
    public function __construct(ResourceMetaInterface $resourceMeta, array $documentConfiguration = [])
    {
        $this->resourceMeta = $resourceMeta;
        $this->documentConfiguration = $documentConfiguration;
    }

    /**
     * @return mixed
     */
    public function getDocumentId()
    {
        return $this->resourceMeta->getDocumentId();
    }

    /**
     * @return ResourceMetaInterface
     */
    public function getResourceMeta()
    {
        return $this->resourceMeta;
    }

    /**
     * @return array
     */
    public function getDocumentConfiguration()
    {
        return $this->documentConfiguration;
    }

    /**
     * @param OptionFieldContainerInterface $fieldContainer
     */
    public function addOptionField(OptionFieldContainerInterface $fieldContainer)
    {
        $this->optionFields[] = $fieldContainer;
    }

    /**
     * @param IndexFieldContainerInterface $fieldContainer
     */
    public function addIndexField(IndexFieldContainerInterface $fieldContainer)
    {
        $this->indexFields[] = $fieldContainer;
    }

    /**
     * @return bool
     */
    public function hasIndexFields()
    {
        return is_array($this->indexFields) && count($this->indexFields) > 0;
    }

    /**
     * @return array|IndexFieldContainerInterface[]
     */
    public function getIndexFields()
    {
        return !$this->hasIndexFields() ? [] : $this->indexFields;
    }

    /**
     * @return bool
     */
    public function hasOptionFields()
    {
        return is_array($this->optionFields) && count($this->optionFields) > 0;
    }

    /**
     * @return array|OptionFieldContainerInterface[]
     */
    public function getOptionFields()
    {
        return !$this->hasOptionFields() ? [] : $this->optionFields;
    }
}