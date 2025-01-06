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

use DynamicSearchBundle\Resource\FieldTransformerInterface;
use DynamicSearchBundle\Resource\ResourceScaffolderInterface;

interface TransformerRegistryInterface
{
    public function hasResourceFieldTransformer(string $resourceScaffolderName, string $identifier): bool;

    public function getResourceFieldTransformer(string $resourceScaffolderName, string $identifier): FieldTransformerInterface;

    /**
     * @return array<int, ResourceScaffolderInterface>
     */
    public function getAllResourceScaffolderForDataProvider(string $dataProviderName): array;
}
