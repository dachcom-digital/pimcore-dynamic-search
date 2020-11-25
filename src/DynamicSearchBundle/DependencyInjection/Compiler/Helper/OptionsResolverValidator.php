<?php

namespace DynamicSearchBundle\DependencyInjection\Compiler\Helper;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OptionsResolverValidator
{
    /**
     * @param ContainerBuilder $container
     * @param array            $contextService
     * @param array            $serviceDefinitionStack
     *
     * @return array
     */
    public function validate(ContainerBuilder $container, array $contextService, array $serviceDefinitionStack)
    {
        if (!$container->hasParameter('dynamic_search.context.full_configuration')) {
            return [];
        }

        $definition = null;

        $service = $contextService['serviceName'] ?? null;
        $options = $contextService['options'] ?? null;

        if ($service === null) {
            return [];
        }

        if ($options === null) {
            return [];
        }

        foreach ($serviceDefinitionStack as $optionProviderClass) {
            if ($service === $optionProviderClass['serviceName']) {
                $definition = $container->getDefinition($optionProviderClass['id']) ? $container->getDefinition($optionProviderClass['id']) : null;
                break;
            }
        }

        if ($definition === null) {
            return [];
        }

        $class = $definition->getClass();
        if (!method_exists($class, 'configureOptions')) {
            return [];
        }

        $optionsResolver = new OptionsResolver();
        $class::configureOptions($optionsResolver);

        try {
            $resolvedOptions = $optionsResolver->resolve($options);
        } catch (\Throwable $e) {
            throw new InvalidConfigurationException(sprintf('Invalid "%s" index provider options. %s', $service, $e->getMessage()));
        }

        return $resolvedOptions;
    }
}
