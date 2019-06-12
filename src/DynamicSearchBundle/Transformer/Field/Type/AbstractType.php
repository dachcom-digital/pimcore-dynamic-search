<?php

namespace DynamicSearchBundle\Transformer\Field\Type;

abstract class AbstractType implements TypeInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var bool
     */
    protected $indexed;

    /**
     * @var bool
     */
    protected $stored;

    /**
     * @var int
     */
    protected $boost;

    /**
     * @var array
     */
    protected $properties;

    /**
     * {@inheritDoc}
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritDoc}
     */
    public function setIndexed(bool $indexed)
    {
        $this->indexed = $indexed;
    }

    /**
     * {@inheritDoc}
     */
    public function getIndexed()
    {
        return $this->indexed;
    }

    /**
     * {@inheritDoc}
     */
    public function setStored(bool $stored)
    {
        $this->stored = $stored;
    }

    /**
     * {@inheritDoc}
     */
    public function getStored()
    {
        return $this->stored;
    }

    /**
     * {@inheritDoc}
     */
    public function setBoost(int $boost)
    {
        $this->boost = $boost;
    }

    /**
     * {@inheritDoc}
     */
    public function getBoost()
    {
        return $this->boost;
    }

    /**
     * {@inheritDoc}
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;
    }

    /**
     * {@inheritDoc}
     */
    public function getProperties()
    {
        return $this->properties;
    }
}