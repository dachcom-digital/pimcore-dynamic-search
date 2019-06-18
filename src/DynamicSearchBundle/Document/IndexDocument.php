<?php

namespace DynamicSearchBundle\Document;

use DynamicSearchBundle\Transformer\Container\FieldContainerInterface;
use Ramsey\Uuid\Uuid;

class IndexDocument
{
    /**
     * @var string
     */
    protected $uuid;

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
     * @param array  $options
     * @param string $dispatchTransformerName
     *
     * @throws \Exception
     */
    public function __construct(array $options, string $dispatchTransformerName)
    {
        $this->options = $options;
        $this->dispatchTransformerName = $dispatchTransformerName;
        $this->uuid = Uuid::uuid4()->toString();
    }

    /**
     * @return string
     */
    public function getUUid()
    {
        return $this->uuid;
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
    public function hasDocumentOptions($key)
    {
        return isset($this->options[$key]);
    }

    /**
     * @param mixed $key
     *
     * @return mixed
     */
    public function getDocumentOptions($key)
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