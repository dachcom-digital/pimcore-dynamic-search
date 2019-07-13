<?php

namespace DynamicSearchBundle\Processor\Harmonizer;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;

interface ResourceHarmonizerInterface
{
    /**
     * @param ContextDataInterface $contextData
     * @param                      $resource
     *
     * @return array|NormalizedDataResourceInterface[]|null
     */
    public function harmonizeUntilNormalizedResourceStack(ContextDataInterface $contextData, $resource);

    /**
     * @param ContextDataInterface $contextData
     * @param                      $resource
     *
     * @return null|ResourceContainerInterface
     */
    public function harmonizeUntilResourceContainer(ContextDataInterface $contextData, $resource);
}