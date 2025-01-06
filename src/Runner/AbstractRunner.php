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

use DynamicSearchBundle\Builder\ContextDefinitionBuilderInterface;
use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\ProcessCancelledException;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Exception\RuntimeException;
use DynamicSearchBundle\Exception\SilentException;
use DynamicSearchBundle\Generator\IndexDocumentGeneratorInterface;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\DataManagerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;
use DynamicSearchBundle\Provider\PreConfiguredIndexProviderInterface;

abstract class AbstractRunner
{
    protected LoggerInterface $logger;
    protected ConfigurationInterface $configuration;
    protected ContextDefinitionBuilderInterface $contextDefinitionBuilder;
    protected DataManagerInterface $dataManager;
    protected IndexManagerInterface $indexManager;
    protected IndexDocumentGeneratorInterface $indexDocumentGenerator;

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function setContextDefinitionBuilder(ContextDefinitionBuilderInterface $contextDefinitionBuilder): void
    {
        $this->contextDefinitionBuilder = $contextDefinitionBuilder;
    }

    public function setDataManager(DataManagerInterface $dataManager): void
    {
        $this->dataManager = $dataManager;
    }

    public function setIndexManager(IndexManagerInterface $indexManager): void
    {
        $this->indexManager = $indexManager;
    }

    public function setIndexDocumentGenerator(IndexDocumentGeneratorInterface $indexDocumentGenerator): void
    {
        $this->indexDocumentGenerator = $indexDocumentGenerator;
    }

    /**
     * @throws RuntimeException
     */
    protected function setupProviders(ContextDefinitionInterface $contextDefinition, string $dataProviderBehaviour): array
    {
        return [
            'dataProvider'  => $this->setupDataProvider($contextDefinition, $dataProviderBehaviour),
            'indexProvider' => $this->setupIndexProvider($contextDefinition)
        ];
    }

    /**
     * @throws RuntimeException
     */
    protected function setupIndexProvider(ContextDefinitionInterface $contextDefinition): IndexProviderInterface
    {
        try {
            $indexProvider = $this->indexManager->getIndexProvider($contextDefinition);
        } catch (ProviderException $e) {
            throw new RuntimeException(sprintf('Error while fetching provider. Error was: %s', $e->getMessage()));
        }

        if (!$indexProvider instanceof PreConfiguredIndexProviderInterface) {
            return $indexProvider;
        }

        try {
            $this->setupPreConfiguredIndexProvider($contextDefinition, $indexProvider);
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf('Error pre configuring index provider. Error was: %s', $e->getMessage()));
        }

        return $indexProvider;
    }

    protected function setupDataProvider(
        ContextDefinitionInterface $contextDefinition,
        string $providerBehaviour = DataProviderInterface::PROVIDER_BEHAVIOUR_FULL_DISPATCH
    ): DataProviderInterface {
        try {
            $dataProvider = $this->dataManager->getDataProvider($contextDefinition, $providerBehaviour);
        } catch (ProviderException $e) {
            throw new RuntimeException(sprintf('Error while fetching provider. Error was: %s', $e->getMessage()));
        }

        return $dataProvider;
    }

    /**
     * @throws RuntimeException
     */
    protected function setupContextDefinition(string $contextName, string $dispatchType): ContextDefinitionInterface
    {
        $contextDefinition = $this->contextDefinitionBuilder->buildContextDefinition($contextName, $dispatchType);

        if (!$contextDefinition instanceof ContextDefinitionInterface) {
            throw new RuntimeException(sprintf('Context definition "%s" does not exist', $contextName));
        }

        return $contextDefinition;
    }

    /**
     * @throws \Exception
     */
    private function setupPreConfiguredIndexProvider(ContextDefinitionInterface $contextDefinition, PreConfiguredIndexProviderInterface $indexProvider): void
    {
        try {
            $indexDocument = $this->indexDocumentGenerator->generateWithoutData($contextDefinition, ['preConfiguredIndexProvider' => true]);
        } catch (\Throwable $e) {
            throw new \Exception(
                sprintf(
                    '%s. (The current context index provider also requires pre-configured indices. Please make sure your document definition implements the "%s" interface)',
                    $e->getMessage(),
                    PreConfiguredIndexProviderInterface::class
                )
            );
        }

        if (!$indexDocument->hasIndexFields()) {
            throw new \Exception(
                sprintf(
                    'No Index Document found. The current context index provider requires pre-configured indices. Please make sure your document definition implements the "%s" interface',
                    PreConfiguredIndexProviderInterface::class
                )
            );
        }

        $indexProvider->preConfigureIndex($indexDocument);
    }

    /**
     * @throws SilentException
     */
    protected function warmUpProvider(ContextDefinitionInterface $contextDefinition, array $providers): void
    {
        foreach ($providers as $provider) {
            $providerName = $provider instanceof DataProviderInterface ? $contextDefinition->getDataProviderName() : $contextDefinition->getIndexProviderName();
            $this->logger->log('DEBUG', sprintf('warm up provider'), $providerName, $contextDefinition->getName());

            try {
                $provider->warmUp($contextDefinition);
            } catch (ProcessCancelledException $e) {
                $errorMessage = sprintf('Process has been cancelled. Message was: %s. Process canceling has been initiated', $e->getMessage());
                $this->dispatchCancelledProcessToProviders($errorMessage, $contextDefinition, $providers);
            } catch (RuntimeException $e) {
                $this->dispatchFailOverToProviders(sprintf(
                    'Error pre configure index provider. Error was: %s [Line: %s, File %s]. FailOver has been initiated',
                    $e->getMessage(),
                    $e->getLine(),
                    $e->getFile()
                ), $contextDefinition, $providers);

                throw new SilentException(sprintf('Error on warm up provider'));
            } catch (\Throwable $e) {
                $this->dispatchFailOverToProviders(sprintf(
                    'Error while warming up provider. Error was: %s [Line: %s, File %s]. FailOver has been initiated',
                    $e->getMessage(),
                    $e->getLine(),
                    $e->getFile()
                ), $contextDefinition, $providers);

                throw new SilentException(sprintf('Error on warm up provider'));
            }
        }
    }

    /**
     * @throws SilentException
     */
    protected function coolDownProvider(ContextDefinitionInterface $contextDefinition, array $providers): void
    {
        foreach ($providers as $provider) {
            $providerName = $provider instanceof DataProviderInterface ? $contextDefinition->getDataProviderName() : $contextDefinition->getIndexProviderName();
            $this->logger->log('DEBUG', sprintf('cooling down provider'), $providerName, $contextDefinition->getName());

            try {
                $provider->coolDown($contextDefinition);
            } catch (ProcessCancelledException $e) {
                $this->dispatchCancelledProcessToProviders(sprintf(
                    'Process has been cancelled. Message was: %s. Process canceling has been initiated',
                    $e->getMessage()
                ), $contextDefinition, $providers);

                throw new SilentException(sprintf('Error on cooling down provider'));
            } catch (\Throwable $e) {
                $this->dispatchFailOverToProviders(sprintf(
                    'Error while cooling down provider. Error was: %s [Line: %s, File %s]. FailOver has been initiated',
                    $e->getMessage(),
                    $e->getLine(),
                    $e->getFile()
                ), $contextDefinition, $providers);

                throw new SilentException(sprintf('Error on cooling down provider'));
            }
        }
    }

    /**
     * @throws SilentException
     */
    protected function callSaveMethod(ContextDefinitionInterface $contextDefinition, mixed $class, string $method, array $arguments, array $involvedProviders): void
    {
        try {
            call_user_func_array([$class, $method], $arguments);
        } catch (ProcessCancelledException $e) {
            $this->dispatchCancelledProcessToProviders(sprintf(
                'Process has been cancelled. Message was: %s. Process canceling has been initiated',
                $e->getMessage()
            ), $contextDefinition, $involvedProviders);

            throw new SilentException(sprintf('Error on calling save method'));
        } catch (\Throwable $e) {
            $this->dispatchFailOverToProviders(sprintf(
                'Error while executing data provider. Error was: %s [Line: %s, File %s]. FailOver has been initiated',
                $e->getMessage(),
                $e->getLine(),
                $e->getFile()
            ), $contextDefinition, $involvedProviders);

            throw new SilentException(sprintf('Error on calling save method'));
        }
    }

    protected function dispatchCancelledProcessToProviders(string $errorMessage, ContextDefinitionInterface $contextDefinition, array $providers): void
    {
        $this->logger->error($errorMessage, 'workflow', $contextDefinition->getName());

        foreach ($providers as $provider) {
            $providerName = $provider instanceof DataProviderInterface ? $contextDefinition->getDataProviderName() : $contextDefinition->getIndexProviderName();
            $this->logger->log('DEBUG', sprintf('executing provider cancelled shutdown'), $providerName, $contextDefinition->getName());

            try {
                $provider->cancelledShutdown($contextDefinition);
            } catch (\Throwable $e) {
                $this->logger->error(
                    sprintf(
                        'Error while dispatching cancelled process. Error was: %s.',
                        $e->getMessage()
                    ),
                    get_class($provider),
                    $contextDefinition->getName()
                );
            }
        }
    }

    protected function dispatchFailOverToProviders(string $errorMessage, ContextDefinitionInterface $contextDefinition, array $providers): void
    {
        $this->logger->error($errorMessage, 'workflow', $contextDefinition->getName());

        foreach ($providers as $provider) {
            $providerName = $provider instanceof DataProviderInterface ? $contextDefinition->getDataProviderName() : $contextDefinition->getIndexProviderName();
            $this->logger->log('DEBUG', sprintf('executing provider emergency shutdown'), $providerName, $contextDefinition->getName());

            try {
                $provider->emergencyShutdown($contextDefinition);
            } catch (\Throwable $e) {
                $this->logger->error(sprintf(
                    'Error while dispatching fail over. Error was: %s.',
                    $e->getMessage()
                ), 'workflow', $contextDefinition->getName());
            }
        }
    }
}
