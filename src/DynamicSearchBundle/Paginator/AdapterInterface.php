<?php

namespace DynamicSearchBundle\Paginator;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;

interface AdapterInterface extends \Zend\Paginator\Adapter\AdapterInterface
{
    /**
     * @param ContextDefinitionInterface $context
     */
    public function setContextDefinition(ContextDefinitionInterface $context);

    /**
     * @param string $outputChannelName
     */
    public function setOutputChannelName(string $outputChannelName);

    /**
     * @param DocumentNormalizerInterface $documentNormalizer
     */
    public function setDocumentNormalizer(?DocumentNormalizerInterface $documentNormalizer);
}
