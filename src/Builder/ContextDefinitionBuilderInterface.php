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

namespace DynamicSearchBundle\Builder;

use DynamicSearchBundle\Context\ContextDefinitionInterface;

interface ContextDefinitionBuilderInterface
{
    public function buildContextDefinition(string $contextName, string $dispatchType, array $runtimeValues = []): ?ContextDefinitionInterface;

    /**
     * @return array<int, ContextDefinitionInterface>
     */
    public function buildContextDefinitionStack(string $dispatchType, array $runtimeValues = []): array;
}
