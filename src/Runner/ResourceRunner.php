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

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Processor\ResourceDeletionProcessorInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;

class ResourceRunner extends AbstractRunner implements ResourceRunnerInterface
{
    public function __construct(protected ResourceDeletionProcessorInterface $resourceDeletionProcessor)
    {
    }

    public function runResourceStack(string $contextName, string $dispatchType, array $resourceMetaStack): void
    {
        if ($dispatchType === ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_DELETE) {
            $this->runDeleteStack($contextName, $resourceMetaStack);

            return;
        }

        $contextDefinition = $this->setupContextDefinition($contextName, $dispatchType);

        $providers = $this->setupProviders($contextDefinition, DataProviderInterface::PROVIDER_BEHAVIOUR_SINGLE_DISPATCH);

        $this->warmUpProvider($contextDefinition, $providers);

        /** @var DataProviderInterface $dataProvider */
        $dataProvider = $providers['dataProvider'];

        foreach ($resourceMetaStack as $resourceMeta) {
            $this->callSaveMethod($contextDefinition, $dataProvider, 'provideSingle', [$contextDefinition, $resourceMeta], $providers);
        }

        $this->coolDownProvider($contextDefinition, $providers);
    }

    public function runResource(string $contextName, string $dispatchType, ResourceMetaInterface $resourceMeta): void
    {
        $contextDefinition = $this->setupContextDefinition($contextName, $dispatchType);

        $providers = $this->setupProviders($contextDefinition, DataProviderInterface::PROVIDER_BEHAVIOUR_SINGLE_DISPATCH);

        $this->warmUpProvider($contextDefinition, $providers);

        /** @var DataProviderInterface $dataProvider */
        $dataProvider = $providers['dataProvider'];

        $this->callSaveMethod($contextDefinition, $dataProvider, 'provideSingle', [$contextDefinition, $resourceMeta], $providers);
        $this->coolDownProvider($contextDefinition, $providers);
    }

    public function runDelete(string $contextName, ResourceMetaInterface $resourceMeta): void
    {
        $contextDefinition = $this->setupContextDefinition($contextName, ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_DELETE);

        $indexProvider = $this->setupIndexProvider($contextDefinition);

        $this->warmUpProvider($contextDefinition, [$indexProvider]);
        $this->callSaveMethod($contextDefinition, $this->resourceDeletionProcessor, 'processByResourceMeta', [$contextDefinition, $resourceMeta], [$indexProvider]);
        $this->coolDownProvider($contextDefinition, [$indexProvider]);
    }

    public function runDeleteStack(string $contextName, array $resourceMetaStack): void
    {
        $contextDefinition = $this->setupContextDefinition($contextName, ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_DELETE);

        $indexProvider = $this->setupIndexProvider($contextDefinition);

        $this->warmUpProvider($contextDefinition, [$indexProvider]);

        foreach ($resourceMetaStack as $resourceMeta) {
            $this->callSaveMethod($contextDefinition, $this->resourceDeletionProcessor, 'processByResourceMeta', [$contextDefinition, $resourceMeta], [$indexProvider]);
        }

        $this->coolDownProvider($contextDefinition, [$indexProvider]);
    }
}
