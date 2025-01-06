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

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\Resolver\ResourceScaffolderNotFoundException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Registry\TransformerRegistryInterface;
use DynamicSearchBundle\Resolver\ResourceScaffolderResolverInterface;
use DynamicSearchBundle\Resource\FieldTransformerInterface;
use DynamicSearchBundle\Resource\ResourceScaffolderContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransformerManager implements TransformerManagerInterface
{
    public function __construct(
        protected LoggerInterface $logger,
        protected ConfigurationInterface $configuration,
        protected ResourceScaffolderResolverInterface $documentTransformerResolver,
        protected TransformerRegistryInterface $transformerRegistry
    ) {
    }

    public function getResourceScaffolder(ContextDefinitionInterface $contextDefinition, $resource): ?ResourceScaffolderContainerInterface
    {
        $resourceScaffolderContainer = null;
        $dataProviderName = $contextDefinition->getDataProviderName();

        try {
            $resourceScaffolderContainer = $this->documentTransformerResolver->resolve($contextDefinition->getDataProviderName(), $resource);
        } catch (ResourceScaffolderNotFoundException $e) {
            // fail silently to log incident
        }

        if (!$resourceScaffolderContainer instanceof ResourceScaffolderContainerInterface) {
            $this->logger->error('No Resource Scaffolder found for new data', $dataProviderName, $contextDefinition->getName());

            return null;
        }

        return $resourceScaffolderContainer;
    }

    public function getResourceFieldTransformer(string $dispatchTransformerName, string $fieldTransformerName, array $transformerOptions = []): ?FieldTransformerInterface
    {
        if (!$this->transformerRegistry->hasResourceFieldTransformer($dispatchTransformerName, $fieldTransformerName)) {
            return null;
        }

        $fieldTransformer = $this->transformerRegistry->getResourceFieldTransformer($dispatchTransformerName, $fieldTransformerName);

        $optionsResolver = new OptionsResolver();
        $fieldTransformer->configureOptions($optionsResolver);
        $fieldTransformer->setOptions($optionsResolver->resolve($transformerOptions));

        return $fieldTransformer;
    }
}
