<?php

namespace DynamicSearchBundle\Processor\Harmonizer;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;

interface ResourceHarmonizerInterface
{
    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param mixed                      $resource
     *
     * @return array|NormalizedDataResourceInterface[]|null
     */
    public function harmonizeUntilNormalizedResourceStack(ContextDefinitionInterface $contextDefinition, $resource);

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param mixed                      $resource
     *
     * @return null|ResourceContainerInterface
     */
    public function harmonizeUntilResourceContainer(ContextDefinitionInterface $contextDefinition, $resource);
}
