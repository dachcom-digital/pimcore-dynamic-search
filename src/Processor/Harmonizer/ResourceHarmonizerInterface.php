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

namespace DynamicSearchBundle\Processor\Harmonizer;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;

interface ResourceHarmonizerInterface
{
    /**
     * @return null|array<int, NormalizedDataResourceInterface>
     */
    public function harmonizeUntilNormalizedResourceStack(ContextDefinitionInterface $contextDefinition, mixed $resource): ?array;

    public function harmonizeUntilResourceContainer(ContextDefinitionInterface $contextDefinition, mixed $resource): ?ResourceContainerInterface;
}
