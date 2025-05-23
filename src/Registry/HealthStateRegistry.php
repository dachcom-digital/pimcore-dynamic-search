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
use DynamicSearchBundle\State\HealthStateInterface;

class HealthStateRegistry implements HealthStateRegistryInterface
{
    protected RegistryStorage $registryStorage;

    public function __construct()
    {
        $this->registryStorage = new RegistryStorage();
    }

    public function register(HealthStateInterface $service): void
    {
        $this->registryStorage->store($service, HealthStateInterface::class, 'healthState', get_class($service));
    }

    public function all(): array
    {
        return $this->registryStorage->getByNamespace('healthState');
    }
}
