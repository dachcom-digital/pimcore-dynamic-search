<?php

namespace DynamicSearchBundle\DependencyInjection\Compiler;

use DynamicSearchBundle\Registry\DefinitionBuilderRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class DefinitionBuilderPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition(DefinitionBuilderRegistry::class);

        foreach ($this->findAndSortTaggedServices('dynamic_search.document_definition_builder', $container) as $reference) {
            $definition->addMethodCall('registerDocumentDefinition', [$reference]);
        }

        foreach ($this->findAndSortTaggedServices('dynamic_search.filter_definition_builder', $container) as $reference) {
            $definition->addMethodCall('registerFilterDefinition', [$reference]);
        }
    }
}
