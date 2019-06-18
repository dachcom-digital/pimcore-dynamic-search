<?php

namespace DynamicSearchBundle\Transformer\Container;

interface DataContainerInterface
{
    /**
     * @return array
     */
    public function getData();

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function hasDataAttribute($attribute);

    /**
     * @param string $attribute
     *
     * @return mixed
     */
    public function getDataAttribute($attribute);
}
