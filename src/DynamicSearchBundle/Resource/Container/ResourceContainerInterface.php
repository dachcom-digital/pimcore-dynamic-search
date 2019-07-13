<?php

namespace DynamicSearchBundle\Resource\Container;

interface ResourceContainerInterface
{
    /**
     * @return bool
     */
    public function hasResource();

    /**
     * @return bool
     */
    public function isBaseResource();

    /**
     * @return mixed
     */
    public function getResource();

    /**
     * @return mixed
     */
    public function getResourceScaffolderIdentifier();

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
