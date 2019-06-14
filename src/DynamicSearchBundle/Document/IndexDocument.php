<?php

namespace DynamicSearchBundle\Document;

use DynamicSearchBundle\Transformer\Field\Type\TypeInterface;
use Ramsey\Uuid\Uuid;

class IndexDocument
{
    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var int
     */
    protected $documentBoost;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @param int $documentBoost
     *
     * @throws \Exception
     */
    public function __construct(int $documentBoost = 1)
    {
        $this->documentBoost = $documentBoost;
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
     * @param string $fieldType
     * @param string $name
     * @param mixed  $value
     * @param array  $fieldProperties
     * @param int    $boost
     * @param bool   $indexed
     * @param bool   $stored
     *
     * @throws \Exception
     */
    public function addField(string $fieldType, string $name, $value, array $fieldProperties = [], int $boost = 1, bool $indexed = true, bool $stored = true)
    {
        if (!is_subclass_of($fieldType, TypeInterface::class)) {
            throw new \Exception(sprintf('Could not load type "%s": class does not implement "%s".', $fieldType, TypeInterface::class));
        }

        /** @var TypeInterface $field */
        $field = new $fieldType();
        $field->setName($name);
        $field->setValue($value);
        $field->setIndexed($indexed);
        $field->setStored($stored);
        $field->setBoost($boost);
        $field->setProperties($fieldProperties);

        $this->fields[] = $field;
    }

    public function hasFields()
    {
        return is_array($this->fields) && count($this->fields) > 0;
    }

    /**
     * @return array|TypeInterface[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    public function getDocumentBoost()
    {
        return $this->documentBoost;
    }
}