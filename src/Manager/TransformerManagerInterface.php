<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Resource\FieldTransformerInterface;
use DynamicSearchBundle\Resource\ResourceScaffolderContainerInterface;

interface TransformerManagerInterface
{
    public function getResourceScaffolder(ContextDefinitionInterface $contextDefinition, mixed $resource): ?ResourceScaffolderContainerInterface;

    public function getResourceFieldTransformer(string $dispatchTransformerName, string $fieldTransformerName, array $transformerOptions = []): ?FieldTransformerInterface;
}
