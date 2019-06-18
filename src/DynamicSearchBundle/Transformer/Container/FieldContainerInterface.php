<?php

namespace DynamicSearchBundle\Transformer\Container;

interface FieldContainerInterface
{
    /**
     * @return mixed
     */
    public function getData();

    /**
     * @param string $indexType
     */
    public function setIndexType(string $indexType);

    /**
     * @return string
     */
    public function getIndexType();

    /**
     * @param string $name
     */
    public function setName(string $name);

    /**
     * @return string
     */
    public function getName();

}
