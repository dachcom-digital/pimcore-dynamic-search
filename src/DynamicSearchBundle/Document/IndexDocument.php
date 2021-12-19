<?php

namespace DynamicSearchBundle\Document;

use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Resource\Container\IndexFieldContainerInterface;
use DynamicSearchBundle\Resource\Container\OptionFieldContainerInterface;

class IndexDocument
{
    protected ?ResourceMetaInterface $resourceMeta;
    protected array $documentConfiguration;
    protected array $optionFields = [];
    protected array $indexFields = [];

    public function __construct(?ResourceMetaInterface $resourceMeta, array $documentConfiguration = [])
    {
        $this->resourceMeta = $resourceMeta;
        $this->documentConfiguration = $documentConfiguration;
    }

    public function getDocumentId(): null|string|int
    {
        return $this->resourceMeta instanceof ResourceMetaInterface ? $this->resourceMeta->getDocumentId() : null;
    }

    public function getResourceMeta(): ?ResourceMetaInterface
    {
        return $this->resourceMeta;
    }

    public function getDocumentConfiguration(): array
    {
        return $this->documentConfiguration;
    }

    public function addOptionField(OptionFieldContainerInterface $fieldContainer): void
    {
        $this->optionFields[] = $fieldContainer;
    }

    public function addIndexField(IndexFieldContainerInterface $fieldContainer): void
    {
        $this->indexFields[] = $fieldContainer;
    }

    public function hasIndexFields(): bool
    {
        return is_array($this->indexFields) && count($this->indexFields) > 0;
    }

    /**
     * @return array<int, IndexFieldContainerInterface>
     */
    public function getIndexFields(): array
    {
        return !$this->hasIndexFields() ? [] : $this->indexFields;
    }

    public function hasOptionFields(): bool
    {
        return is_array($this->optionFields) && count($this->optionFields) > 0;
    }

    /**
     * @return array<int, OptionFieldContainerInterface>
     */
    public function getOptionFields(): array
    {
        return !$this->hasOptionFields() ? [] : $this->optionFields;
    }
}
