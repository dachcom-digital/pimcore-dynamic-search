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

use DynamicSearchBundle\Registry\IndexRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class IndexPass implements CompilerPassInterface
{
    public const INDEX_FIELD_TAG = 'dynamic_search.index.field';
    public const INDEX_FILTER_TAG = 'dynamic_search.index.filter';

    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds(self::INDEX_FIELD_TAG, true) as $id => $tags) {
            $definition = $container->getDefinition(IndexRegistry::class);
            foreach ($tags as $attributes) {
                $alias = $attributes['identifier'] ?? null;
                $definition->addMethodCall('registerField', [new Reference($id), $id, $alias, $attributes['index_provider']]);
            }
        }

        foreach ($container->findTaggedServiceIds(self::INDEX_FILTER_TAG, true) as $id => $tags) {
            $definition = $container->getDefinition(IndexRegistry::class);
            foreach ($tags as $attributes) {
                $alias = $attributes['identifier'] ?? null;
                $definition->addMethodCall('registerFilter', [new Reference($id), $id, $alias, $attributes['index_provider']]);
            }
        }
    }
}
