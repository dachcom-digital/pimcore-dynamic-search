<?php

namespace DynamicSearchBundle\Paginator;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;

interface AdapterInterface
{
    public function setContextDefinition(ContextDefinitionInterface $contextDefinition): void;

    public function setOutputChannelName(string $outputChannelName): void;

    public function setDocumentNormalizer(?DocumentNormalizerInterface $documentNormalizer): void;
}
