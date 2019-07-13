<?php

namespace DynamicSearchBundle\Resource;

use DynamicSearchBundle\Context\ContextDataInterface;

interface ResourceScaffolderInterface
{
    /**
     * @param mixed $resource
     *
     * @return bool
     */
    public function isApplicable($resource): bool;

    /**
     * @param $resource
     *
     * @return bool
     */
    public function isBaseResource($resource);

    /**
     * @param ContextDataInterface $contextData
     * @param mixed                $resource
     *
     * @return array
     */
    public function setup(ContextDataInterface $contextData, $resource): array;
}
