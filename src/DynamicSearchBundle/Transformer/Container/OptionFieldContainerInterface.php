<?php

namespace DynamicSearchBundle\Transformer\Container;

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
