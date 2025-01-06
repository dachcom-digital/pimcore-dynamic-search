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

namespace DynamicSearchBundle\DependencyInjection\Compiler;

use DynamicSearchBundle\Registry\DefinitionBuilderRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class DefinitionBuilderPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public const DOCUMENT_DEFINITION_BUILDER = 'dynamic_search.document_definition_builder';
    public const FILTER_DEFINITION_BUILDER = 'dynamic_search.filter_definition_builder';

    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition(DefinitionBuilderRegistry::class);

        foreach ($this->findAndSortTaggedServices(self::DOCUMENT_DEFINITION_BUILDER, $container) as $reference) {
            $definition->addMethodCall('registerDocumentDefinition', [$reference]);
        }

        foreach ($this->findAndSortTaggedServices(self::FILTER_DEFINITION_BUILDER, $container) as $reference) {
            $definition->addMethodCall('registerFilterDefinition', [$reference]);
        }
    }
}
