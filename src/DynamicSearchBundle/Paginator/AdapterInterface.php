<?php

namespace DynamicSearchBundle\Paginator;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;

interface AdapterInterface extends \Laminas\Paginator\Adapter\AdapterInterface
{
    public function setContextDefinition(ContextDefinitionInterface $context): void;

    public function setOutputChannelName(string $outputChannelName): void;

    public function setDocumentNormalizer(?DocumentNormalizerInterface $documentNormalizer): void;
}
