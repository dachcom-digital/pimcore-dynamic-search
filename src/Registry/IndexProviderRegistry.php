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

use DynamicSearchBundle\Provider\IndexProviderInterface;
use DynamicSearchBundle\Registry\Storage\RegistryStorage;

class IndexProviderRegistry implements IndexProviderRegistryInterface
{
    protected RegistryStorage $registryStorage;

    public function __construct()
    {
        $this->registryStorage = new RegistryStorage();
    }

    public function register(IndexProviderInterface $service, string $identifier, ?string $alias): void
    {
        $this->registryStorage->store($service, IndexProviderInterface::class, 'indexProvider', $identifier, $alias);
    }

    public function has(string $identifier): bool
    {
        return $this->registryStorage->has('indexProvider', $identifier);
    }

    public function get(string $identifier): ?IndexProviderInterface
    {
        return $this->registryStorage->get('indexProvider', $identifier);
    }

    public function all(): array
    {
        return $this->registryStorage->getByNamespace('indexProvider');
    }
}
