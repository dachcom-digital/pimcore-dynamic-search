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
use DynamicSearchBundle\Factory\ContextDefinitionFactoryInterface;

class ContextDefinitionBuilder implements ContextDefinitionBuilderInterface
{
    public function __construct(protected ContextDefinitionFactoryInterface $contextDefinitionFactory)
    {
    }

    public function buildContextDefinition(string $contextName, string $dispatchType, array $runtimeValues = []): ?ContextDefinitionInterface
    {
        return $this->contextDefinitionFactory->createSingle($contextName, $dispatchType, $runtimeValues);
    }

    public function buildContextDefinitionStack(string $dispatchType, array $runtimeValues = []): array
    {
        return $this->contextDefinitionFactory->createStack($dispatchType, $runtimeValues);
    }
}
