<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\Normalizer\ResourceNormalizerInterface;

interface NormalizerManagerInterface
{
    public function getResourceNormalizer(ContextDefinitionInterface $contextDefinition): ?ResourceNormalizerInterface;

    public function getDocumentNormalizerForOutputChannel(
        ContextDefinitionInterface $contextDefinition,
        string $outputChannelName
    ): ?DocumentNormalizerInterface;
}
