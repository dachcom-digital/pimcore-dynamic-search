<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\NormalizerException;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\Normalizer\ResourceNormalizerInterface;

interface NormalizerManagerInterface
{
    /**
     * @throws NormalizerException
     */
    public function getResourceNormalizer(ContextDefinitionInterface $contextDefinition): ?ResourceNormalizerInterface;

    /**
     * @throws NormalizerException
     */
    public function getDocumentNormalizerForOutputChannel(ContextDefinitionInterface $contextDefinition, string $outputChannelName): ?DocumentNormalizerInterface;
}
