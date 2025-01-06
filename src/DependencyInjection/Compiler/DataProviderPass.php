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

use DynamicSearchBundle\DependencyInjection\Compiler\Helper\OptionsResolverValidator;
use DynamicSearchBundle\Factory\ContextDefinitionFactory;
use DynamicSearchBundle\Registry\DataProviderRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class DataProviderPass implements CompilerPassInterface
{
    public const DATA_PROVIDER_TAG = 'dynamic_search.data_provider';

    public function process(ContainerBuilder $container): void
    {
        $serviceDefinitionStack = [];
        $definition = $container->getDefinition(DataProviderRegistry::class);

        foreach ($container->findTaggedServiceIds(self::DATA_PROVIDER_TAG, true) as $id => $tags) {
            foreach ($tags as $attributes) {
                $alias = $attributes['identifier'] ?? null;
                $serviceName = $alias ?? $id;

                $serviceDefinitionStack[] = ['serviceName' => $serviceName, 'id' => $id];
                $definition->addMethodCall('register', [new Reference($id), $id, $alias]);
            }
        }

        $this->validateOptions($container, $serviceDefinitionStack);
    }

    private function validateOptions(ContainerBuilder $container, array $serviceDefinitionStack): void
    {
        /* @phpstan-ignore-next-line */
        if (!$container->hasParameter('dynamic_search.context.full_configuration')) {
            return;
        }

        $validator = new OptionsResolverValidator();
        $contextDefinitionFactory = $container->getDefinition(ContextDefinitionFactory::class);
        $contextConfiguration = $container->getParameter('dynamic_search.context.full_configuration');

        foreach ($contextConfiguration as $contextName => &$contextConfig) {
            $contextService = [
                'serviceName' => $contextConfig['data_provider']['service'] ?? null,
                'options'     => $contextConfig['data_provider']['options'] ?? null
            ];

            $contextConfig['data_provider']['options'] = $validator->validate($container, $contextService, $serviceDefinitionStack);
            $contextDefinitionFactory->addMethodCall('replaceContextConfig', [$contextName, $contextConfig]);
        }

        $container->setParameter('dynamic_search.context.full_configuration', $contextConfiguration);
    }
}
