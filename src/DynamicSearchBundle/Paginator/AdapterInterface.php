<?php

namespace DynamicSearchBundle\Paginator;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;

interface AdapterInterface extends \Zend\Paginator\Adapter\AdapterInterface
{
    /**
     * @param ContextDataInterface $context
     */
    public function setContext(ContextDataInterface $context);

    /**
     * @param string $outputChannelName
     */
    public function setOutputChannelName(string $outputChannelName);

    /**
     * @param DocumentNormalizerInterface $documentNormalizer
     */
    public function setDocumentNormalizer(?DocumentNormalizerInterface $documentNormalizer);
}
