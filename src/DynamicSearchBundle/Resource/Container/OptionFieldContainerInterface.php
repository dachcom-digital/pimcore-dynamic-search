<?php

namespace DynamicSearchBundle\Resource\Container;

interface OptionFieldContainerInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return mixed
     */
    public function getData();
}
