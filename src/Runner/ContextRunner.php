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
use DynamicSearchBundle\Exception\RuntimeException;
use DynamicSearchBundle\Exception\SilentException;
use DynamicSearchBundle\Manager\QueueManagerInterface;
use DynamicSearchBundle\Service\LongProcessServiceInterface;

class ContextRunner extends AbstractRunner implements ContextRunnerInterface
{
    public function __construct(
        protected QueueManagerInterface $queueManager,
        protected LongProcessServiceInterface $longProcessService
    ) {
    }

    public function runFullContextCreation(): void
    {
        $contextDefinitions = $this->contextDefinitionBuilder->buildContextDefinitionStack(ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_INDEX);

        if (count($contextDefinitions) === 0) {
            throw new RuntimeException('No context configuration found. Please add them to the "dynamic_search.context" configuration node');
        }

        $this->queueManager->clearQueue();
        $this->longProcessService->boot();

        foreach ($contextDefinitions as $contextDefinition) {
            $this->dispatchContext($contextDefinition);
        }

        $this->longProcessService->shutdown();
    }

    public function runSingleContextCreation(string $contextName): void
    {
        $contextDefinition = $this->setupContextDefinition($contextName, ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_INDEX);

        $this->longProcessService->boot();

        $this->dispatchContext($contextDefinition);

        $this->longProcessService->shutdown();
    }

    /**
     * @throws SilentException
     */
    protected function dispatchContext(ContextDefinitionInterface $contextDefinition): void
    {
        $dataProvider = $this->setupDataProvider($contextDefinition);

        $this->warmUpProvider($contextDefinition, [$dataProvider]);

        $this->logger->log(
            'DEBUG',
            sprintf('execute provider for dispatch type "%s"', $contextDefinition->getContextDispatchType()),
            $contextDefinition->getDataProviderName(),
            $contextDefinition->getName()
        );

        $this->callSaveMethod($contextDefinition, $dataProvider, 'provideAll', [$contextDefinition], [$dataProvider]);
        $this->coolDownProvider($contextDefinition, [$dataProvider]);
    }
}
