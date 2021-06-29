<?php

namespace DynamicSearchBundle\Processor\Harmonizer;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;

interface ResourceHarmonizerInterface
{
    public function harmonizeUntilNormalizedResourceStack(ContextDefinitionInterface $contextDefinition, $resource): ?array;

    public function harmonizeUntilResourceContainer(ContextDefinitionInterface $contextDefinition, $resource): ?ResourceContainerInterface;
}
