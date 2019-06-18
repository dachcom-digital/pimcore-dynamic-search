<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Exception\DispatchTransformerNotFoundException;

interface DataResolverInterface
{
    /**
     * @param mixed $data
     *
     * @return mixed
     *
     * @throws DispatchTransformerNotFoundException
     */
    public function resolve($data);
}