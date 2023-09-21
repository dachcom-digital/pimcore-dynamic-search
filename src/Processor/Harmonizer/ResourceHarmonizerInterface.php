<?php

namespace DynamicSearchBundle\Processor\Harmonizer;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;

interface ResourceHarmonizerInterface
{
    /**
     * @return null|array<int, NormalizedDataResourceInterface>
     */
    public function harmonizeUntilNormalizedResourceStack(ContextDefinitionInterface $contextDefinition, mixed $resource): ?array;

    public function harmonizeUntilResourceContainer(ContextDefinitionInterface $contextDefinition, mixed $resource): ?ResourceContainerInterface;
}
