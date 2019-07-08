<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\Normalizer\ResourceNormalizerInterface;

interface NormalizerManagerInterface
{
    /**
     * @param ContextDataInterface $contextData
     *
     * @return ResourceNormalizerInterface|null
     */
    public function getResourceNormalizer(ContextDataInterface $contextData);

    /**
     * @param ContextDataInterface $contextData
     * @param string               $outputChannelName
     *
     * @return DocumentNormalizerInterface|null
     */
    public function getDocumentNormalizerForOutputChannel(ContextDataInterface $contextData, string $outputChannelName);
}
