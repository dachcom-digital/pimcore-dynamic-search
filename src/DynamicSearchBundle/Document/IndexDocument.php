<?php

namespace DynamicSearchBundle\Document;

use DynamicSearchBundle\Transformer\Container\FieldContainerInterface;

class IndexDocument
{
    /**
     * @var mixed
     */
    protected $documentId;

    /**
     * @var string
     */
    protected $dispatchTransformerName;

    /**
     * @var int
     */
    protected $options;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @param mixed                           $documentId
     * @param array|FieldContainerInterface[] $options
     * @param string                          $dispatchTransformerName
     */
    public function __construct($documentId, array $options, string $dispatchTransformerName)
    {
        $documentOptions = [];
        foreach ($options as $option) {
            $documentOptions[$option->getName()] = $option->getData();
        }

        $this->documentId = $documentId;
        $this->options = $documentOptions;
        $this->dispatchTransformerName = $dispatchTransformerName;
    }

    /**
     * @return mixed
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * @return string
     */
    public function getDispatchedTransformerName()
    {
        return $this->dispatchTransformerName;
    }

    /**
     * @param mixed $key
     *
     * @return bool
     */
    public function hasDocumentOption($key)
    {
        return isset($this->options[$key]);
    }

    /**
     * @param mixed $key
     *
     * @return mixed
     */
    public function getDocumentOption($key)
    {
        return $this->options[$key];
    }

    /**
     * @param mixed                   $indexField
     * @param FieldContainerInterface $fieldContainer
     */
    public function addField($indexField, FieldContainerInterface $fieldContainer)
    {
        $this->fields[] = [
            'indexField'     => $indexField,
            'fieldContainer' => $fieldContainer
        ];
    }

    public function hasFields()
    {
        return is_array($this->fields) && count($this->fields) > 0;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return !$this->hasFields() ? [] : $this->fields;
    }
}