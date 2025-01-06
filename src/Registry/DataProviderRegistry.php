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

use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Registry\Storage\RegistryStorage;

class DataProviderRegistry implements DataProviderRegistryInterface
{
    protected RegistryStorage $registryStorage;

    public function __construct()
    {
        $this->registryStorage = new RegistryStorage();
    }

    public function register(DataProviderInterface $service, string $identifier, ?string $alias): void
    {
        $this->registryStorage->store($service, DataProviderInterface::class, 'dataProvider', $identifier, $alias);
    }

    public function has($identifier): bool
    {
        return $this->registryStorage->has('dataProvider', $identifier);
    }

    public function get($identifier): ?DataProviderInterface
    {
        return $this->registryStorage->get('dataProvider', $identifier);
    }

    public function all(): array
    {
        return $this->registryStorage->getByNamespace('dataProvider');
    }
}
