<?php

namespace DynamicSearchBundle\Transformer\Container;

interface DocumentContainerInterface
{
    /**
     * @return bool
     */
    public function hasResource();

    /**
     * @return mixed
     */
    public function getResource();

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function hasAttribute($attribute);

    /**
     * @param string $attribute
     *
     * @return mixed
     */
    public function getAttribute($attribute);

    /**
     * @return array
     */
    public function getAttributes();
}
