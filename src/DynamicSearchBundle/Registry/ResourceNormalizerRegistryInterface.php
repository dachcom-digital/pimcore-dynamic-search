<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\Normalizer\ResourceNormalizerInterface;

interface ResourceNormalizerRegistryInterface
{
    public function hasResourceNormalizerForDataProvider(string $dataProviderName, string $identifier): bool;

    public function getResourceNormalizerForDataProvider(string $dataProviderName, string $identifier): ResourceNormalizerInterface;

    public function getDocumentNormalizerForIndexProvider(string $indexProviderName, string $identifier): DocumentNormalizerInterface;

    public function hasDocumentNormalizerForIndexProvider(string $indexProviderName, string $identifier): bool;
}
