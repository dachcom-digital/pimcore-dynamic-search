<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Normalizer\ResourceNormalizerInterface;

interface ResourceNormalizerManagerInterface
{
    /**
     * @param ContextDataInterface $contextData
     *
     * @return ResourceNormalizerInterface|null
     */
    public function getResourceNormalizer(ContextDataInterface $contextData);
}
