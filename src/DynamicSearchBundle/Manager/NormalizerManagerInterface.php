<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\NormalizerException;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\Normalizer\ResourceNormalizerInterface;

interface NormalizerManagerInterface
{
    /**
     * @param ContextDataInterface $contextData
     *
     * @return ResourceNormalizerInterface|null
     *
     * @throws NormalizerException
     */
    public function getResourceNormalizer(ContextDataInterface $contextData);

    /**
     * @param ContextDataInterface $contextData
     * @param string               $outputChannelName
     *
     * @return DocumentNormalizerInterface|null
     *
     * @throws NormalizerException
     */
    public function getDocumentNormalizerForOutputChannel(ContextDataInterface $contextData, string $outputChannelName);
}
