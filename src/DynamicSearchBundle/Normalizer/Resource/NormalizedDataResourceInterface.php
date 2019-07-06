<?php

namespace DynamicSearchBundle\Normalizer\Resource;

use DynamicSearchBundle\Transformer\Container\ResourceContainerInterface;

interface NormalizedDataResourceInterface
{
    /**
     * @return ResourceContainerInterface|null
     */
    public function getResourceContainer();

    /**
     * @return mixed
     */
    public function getResourceId();

    /**
     * @return array
     */
    public function getOptions();
}
