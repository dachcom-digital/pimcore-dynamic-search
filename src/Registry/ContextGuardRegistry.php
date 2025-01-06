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

use DynamicSearchBundle\Guard\ContextGuardInterface;
use DynamicSearchBundle\Registry\Storage\RegistryStorage;

class ContextGuardRegistry implements ContextGuardRegistryInterface
{
    protected RegistryStorage $registryStorage;

    public function __construct()
    {
        $this->registryStorage = new RegistryStorage();
    }

    public function register(ContextGuardInterface $service): void
    {
        $this->registryStorage->store($service, ContextGuardInterface::class, 'contextGuard', get_class($service));
    }

    public function getAllGuards(): array
    {
        return $this->registryStorage->getByNamespace('contextGuard');
    }
}
