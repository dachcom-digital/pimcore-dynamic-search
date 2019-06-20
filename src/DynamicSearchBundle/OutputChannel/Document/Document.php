<?php

namespace DynamicSearchBundle\OutputChannel\Document;

class Document implements DocumentInterface
{
    protected $fields;

    /**
     * @param $fields
     */
    public function __construct($fields)
    {
        $this->fields[] = $fields;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getField(string $name)
    {
        return $this->fields[$name];
    }
}