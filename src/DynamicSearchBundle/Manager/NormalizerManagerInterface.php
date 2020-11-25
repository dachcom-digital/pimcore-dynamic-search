<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\NormalizerException;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\Normalizer\ResourceNormalizerInterface;

interface NormalizerManagerInterface
{
    /**
     * @param ContextDefinitionInterface $contextDefinition
     *
     * @return ResourceNormalizerInterface|null
     *
     * @throws NormalizerException
     */
    public function getResourceNormalizer(ContextDefinitionInterface $contextDefinition);

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param string               $outputChannelName
     *
     * @return DocumentNormalizerInterface|null
     *
     * @throws NormalizerException
     */
    public function getDocumentNormalizerForOutputChannel(ContextDefinitionInterface $contextDefinition, string $outputChannelName);
}
