<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Exception\DataTransformerNotFoundException;

interface DataResolverInterface
{
    /**
     * @param mixed $data
     *
     * @return mixed
     *
     * @throws DataTransformerNotFoundException
     */
    public function resolve($data);
}