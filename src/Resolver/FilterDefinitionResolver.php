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

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Exception\Resolver\DefinitionNotFoundException;
use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;
use DynamicSearchBundle\Registry\DefinitionBuilderRegistryInterface;

class FilterDefinitionResolver implements FilterDefinitionResolverInterface
{
    public function __construct(protected DefinitionBuilderRegistryInterface $definitionBuilderRegistry)
    {
    }

    public function resolve(string $contextName, OutputChannelAllocatorInterface $outputChannelAllocator): array
    {
        $builder = [];
        foreach ($this->definitionBuilderRegistry->getAllFilterDefinitionBuilder() as $filterDefinitionBuilder) {
            if ($filterDefinitionBuilder->isApplicable($contextName, $outputChannelAllocator) === true) {
                $builder[] = $filterDefinitionBuilder;
            }
        }

        if (count($builder) === 0) {
            throw new DefinitionNotFoundException('filter');
        }

        return $builder;
    }
}
