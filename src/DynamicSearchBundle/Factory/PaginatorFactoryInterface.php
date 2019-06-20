<?php

namespace DynamicSearchBundle\Factory;

use Zend\Paginator\Paginator;

interface PaginatorFactoryInterface
{
    /**
     * @param string $adapterClass
     * @param mixed  $adapterData
     *
     * @return Paginator
     */
    public function create(string $adapterClass, $adapterData);
}