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

use DynamicSearchBundle\Filter\FilterInterface;
use DynamicSearchBundle\Index\IndexFieldInterface;

interface IndexRegistryInterface
{
    public function hasFieldForIndexProvider(string $indexProviderName, string $identifier): bool;

    public function getFieldForIndexProvider(string $indexProviderName, string $identifier): ?IndexFieldInterface;

    public function hasFilterForIndexProvider(string $indexProviderName, string $identifier): bool;

    public function getFilterForIndexProvider(string $indexProviderName, string $identifier): ?FilterInterface;
}
