<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Normalizer\ResourceNormalizerInterface;

interface ResourceNormalizerRegistryInterface
{
    /**
     * @param string $dataProviderName
     * @param string $identifier
     *
     * @return bool
     */
    public function hasResourceNormalizerForDataProvider(string $dataProviderName, string $identifier);

    /**
     * @param string $dataProviderName
     * @param string $identifier
     *
     * @return ResourceNormalizerInterface
     */
    public function getResourceNormalizerForDataProvider(string $dataProviderName, string $identifier);

    /**
     * @param string $indexProviderName
     * @param string $identifier
     *
     * @return ResourceNormalizerInterface
     */
    public function getDocumentNormalizerForIndexProvider(string $indexProviderName, string $identifier);

    /**
     * @param string $indexProviderName
     * @param string $identifier
     *
     * @return ResourceNormalizerInterface
     */
    public function hasDocumentNormalizerForIndexProvider(string $indexProviderName, string $identifier);
}