<?php

namespace DynamicSearchBundle\DependencyInjection\Compiler;

use DynamicSearchBundle\Registry\DocumentDefinitionBuilderRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class DocumentDefinitionBuilderPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition(DocumentDefinitionBuilderRegistry::class);
        foreach ($this->findAndSortTaggedServices('dynamic_search.document_definition_builder', $container) as $reference) {
            $definition->addMethodCall('register', [$reference]);
        }
    }
}