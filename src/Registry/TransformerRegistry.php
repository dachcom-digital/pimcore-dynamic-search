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

use DynamicSearchBundle\Registry\Storage\RegistryStorage;
use DynamicSearchBundle\Resource\FieldTransformerInterface;
use DynamicSearchBundle\Resource\ResourceScaffolderInterface;

class TransformerRegistry implements TransformerRegistryInterface
{
    protected RegistryStorage $registryStorage;

    public function __construct()
    {
        $this->registryStorage = new RegistryStorage();
    }

    public function registerResourceScaffolder(ResourceScaffolderInterface $service, string $identifier, ?string $alias, string $dataProvider): void
    {
        $this->registryStorage->store($service, ResourceScaffolderInterface::class, $dataProvider, $identifier, $alias);
    }

    public function registerResourceFieldTransformer(FieldTransformerInterface $service, string $identifier, ?string $alias, string $resourceScaffolder): void
    {
        $this->registryStorage->store($service, FieldTransformerInterface::class, $resourceScaffolder, $identifier, $alias);
    }

    public function hasResourceFieldTransformer(string $resourceScaffolderName, string $identifier): bool
    {
        return $this->registryStorage->has($resourceScaffolderName, $identifier);
    }

    public function getResourceFieldTransformer(string $resourceScaffolderName, string $identifier): FieldTransformerInterface
    {
        return $this->registryStorage->get($resourceScaffolderName, $identifier);
    }

    public function getAllResourceScaffolderForDataProvider(string $dataProviderName): array
    {
        return $this->registryStorage->getByNamespace($dataProviderName);
    }
}
