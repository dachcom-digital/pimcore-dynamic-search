<?php

namespace DynamicSearchBundle\DependencyInjection\Compiler;

use DynamicSearchBundle\DependencyInjection\Compiler\Helper\OptionsResolverValidator;
use DynamicSearchBundle\Factory\ContextDefinitionFactory;
use DynamicSearchBundle\Registry\DataProviderRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class DataProviderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $serviceDefinitionStack = [];
        foreach ($container->findTaggedServiceIds('dynamic_search.data_provider', true) as $id => $tags) {
            $definition = $container->getDefinition(DataProviderRegistry::class);
            foreach ($tags as $attributes) {
                $serviceDefinitionStack[] = ['serviceName' => $attributes['identifier'], 'id' => $id];
                $definition->addMethodCall('register', [new Reference($id), $attributes['identifier']]);
            }
        }

        $this->validateOptions($container, $serviceDefinitionStack);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $serviceDefinitionStack
     */
    protected function validateOptions(ContainerBuilder $container, array $serviceDefinitionStack)
    {
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
