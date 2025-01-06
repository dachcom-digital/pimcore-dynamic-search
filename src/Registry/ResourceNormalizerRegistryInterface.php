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

interface ResourceNormalizerRegistryInterface
{
    public function hasResourceNormalizerForDataProvider(string $dataProviderName, string $identifier): bool;

    public function getResourceNormalizerForDataProvider(string $dataProviderName, string $identifier): ResourceNormalizerInterface;

    public function hasDocumentNormalizerForIndexProvider(string $indexProviderName, string $identifier): bool;

    public function getDocumentNormalizerForIndexProvider(string $indexProviderName, string $identifier): DocumentNormalizerInterface;
}
