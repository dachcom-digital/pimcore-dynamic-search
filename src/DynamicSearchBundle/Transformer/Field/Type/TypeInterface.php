<?php

namespace DynamicSearchBundle\Transformer\Field\Type;

interface TypeInterface
{
    /**
     * @param string $name
     */
    public function setName(string $name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param mixed $value
     */
    public function setValue($value);

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param bool $indexed
     */
    public function setIndexed(bool $indexed);

    /**
     * @return bool
     */
    public function getIndexed();

    /**
     * @param bool $stored
     */
    public function setStored(bool $stored);

    /**
     * @return bool
     */
    public function getStored();

    /**
     * @param int $boost
     */
    public function setBoost(int $boost);

    /**
     * @return int
     */
    public function getBoost();

    /**
     * @param array $properties
     */
    public function setProperties(array $properties);

    /**
     * @return array
     */
    public function getProperties();

}