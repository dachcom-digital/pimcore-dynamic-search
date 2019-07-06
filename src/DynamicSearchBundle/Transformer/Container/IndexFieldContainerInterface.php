<?php

namespace DynamicSearchBundle\Transformer\Container;

interface IndexFieldContainerInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return mixed
     */
    public function getData();

    /**
     * @return string
     */
    public function getIndexType();


}
