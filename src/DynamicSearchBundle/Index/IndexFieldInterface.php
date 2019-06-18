<?php

namespace DynamicSearchBundle\Index;

use DynamicSearchBundle\Transformer\Container\FieldContainerInterface;

interface IndexFieldInterface
{
    /**
     * @param FieldContainerInterface $fieldContainer
     *
     * @return mixed
     */
    public function build(FieldContainerInterface $fieldContainer);
}