<?php

namespace DynamicSearchBundle\DependencyInjection\Compiler;

use DynamicSearchBundle\DependencyInjection\Compiler\Helper\OptionsResolverValidator;
use DynamicSearchBundle\Factory\ContextDefinitionFactory;
use DynamicSearchBundle\Registry\IndexProviderRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class IndexProviderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition(IndexProviderRegistry::class);

        $serviceDefinitionStack = [];
        foreach ($container->findTaggedServiceIds('dynamic_search.index_provider', true) as $id => $tags) {
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
                'serviceName' => $contextConfig['index_provider']['service'] ?? null,
                'options'     => $contextConfig['index_provider']['options'] ?? null
            ];

            $contextConfig['index_provider']['options'] = $validator->validate($container, $contextService, $serviceDefinitionStack);

            $contextDefinitionFactory->addMethodCall('replaceContextConfig', [$contextName, $contextConfig]);
        }

        $container->setParameter('dynamic_search.context.full_configuration', $contextConfiguration);
    }
}
