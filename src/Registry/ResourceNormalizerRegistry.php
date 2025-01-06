<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\Normalizer\ResourceNormalizerInterface;
use DynamicSearchBundle\Registry\Storage\RegistryStorage;

class ResourceNormalizerRegistry implements ResourceNormalizerRegistryInterface
{
    protected RegistryStorage $registryStorage;

    public function __construct()
    {
        $this->registryStorage = new RegistryStorage();
    }

    public function registerResourceNormalizer(ResourceNormalizerInterface $service, string $identifier, ?string $alias, string $dataProviderName): void
    {
        $namespace = sprintf('resourceNormalizer_%s', $dataProviderName);
        $this->registryStorage->store($service, ResourceNormalizerInterface::class, $namespace, $identifier, $alias);
    }

    public function registerDocumentNormalizer(DocumentNormalizerInterface $service, string $identifier, ?string $alias, string $indexProviderName): void
    {
        $namespace = sprintf('documentNormalizer_%s', $indexProviderName);
        $this->registryStorage->store($service, DocumentNormalizerInterface::class, $namespace, $identifier, $alias);
    }

    public function hasResourceNormalizerForDataProvider(string $dataProviderName, string $identifier): bool
    {
        $namespace = sprintf('resourceNormalizer_%s', $dataProviderName);

        return $this->registryStorage->has($namespace, $identifier);
    }

    public function getResourceNormalizerForDataProvider(string $dataProviderName, string $identifier): ResourceNormalizerInterface
    {
        $namespace = sprintf('resourceNormalizer_%s', $dataProviderName);

        return $this->registryStorage->get($namespace, $identifier);
    }

    public function hasDocumentNormalizerForIndexProvider(string $indexProviderName, string $identifier): bool
    {
        $namespace = sprintf('documentNormalizer_%s', $indexProviderName);

        return $this->registryStorage->has($namespace, $identifier);
    }

    public function getDocumentNormalizerForIndexProvider(string $indexProviderName, string $identifier): DocumentNormalizerInterface
    {
        $namespace = sprintf('documentNormalizer_%s', $indexProviderName);

        return $this->registryStorage->get($namespace, $identifier);
    }
}
