<?php

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
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var ContextDefinitionBuilderInterface
     */
    protected $contextDefinitionBuilder;

    /**
     * @var DataManagerInterface
     */
    protected $dataManager;

    /**
     * @var IndexManagerInterface
     */
    protected $indexManager;

    /**
     * @var IndexDocumentGeneratorInterface
     */
    protected $indexDocumentGenerator;

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param ConfigurationInterface $configuration
     */
    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param ContextDefinitionBuilderInterface $contextDefinitionBuilder
     */
    public function setContextDefinitionBuilder(ContextDefinitionBuilderInterface $contextDefinitionBuilder)
    {
        $this->contextDefinitionBuilder = $contextDefinitionBuilder;
    }

    /**
     * @param DataManagerInterface $dataManager
     */
    public function setDataManager(DataManagerInterface $dataManager)
    {
        $this->dataManager = $dataManager;
    }

    /**
     * @param IndexManagerInterface $indexManager
     */
    public function setIndexManager(IndexManagerInterface $indexManager)
    {
        $this->indexManager = $indexManager;
    }

    /**
     * @param IndexDocumentGeneratorInterface $indexDocumentGenerator
     */
    public function setIndexDocumentGenerator(IndexDocumentGeneratorInterface $indexDocumentGenerator)
    {
        $this->indexDocumentGenerator = $indexDocumentGenerator;
    }

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param string                     $dataProviderBehaviour
     *
     * @return array
     *
     * @throws RuntimeException
     */
    protected function setupProviders(ContextDefinitionInterface $contextDefinition, string $dataProviderBehaviour)
    {
        return [
            'dataProvider'  => $this->setupDataProvider($contextDefinition, $dataProviderBehaviour),
            'indexProvider' => $this->setupIndexProvider($contextDefinition)
        ];
    }

    /**
     * @param ContextDefinitionInterface $contextDefinition
     *
     * @return IndexProviderInterface
     * @throws RuntimeException
     */
    protected function setupIndexProvider(ContextDefinitionInterface $contextDefinition)
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

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param string                     $providerBehaviour
     *
     * @return DataProviderInterface
     */
    protected function setupDataProvider(
        ContextDefinitionInterface $contextDefinition,
        string $providerBehaviour = DataProviderInterface::PROVIDER_BEHAVIOUR_FULL_DISPATCH
    ) {
        try {
            $dataProvider = $this->dataManager->getDataProvider($contextDefinition, $providerBehaviour);
        } catch (ProviderException $e) {
            throw new RuntimeException(sprintf('Error while fetching provider. Error was: %s', $e->getMessage()));
        }

        return $dataProvider;
    }

    /**
     * @param string $contextName
     * @param string $dispatchType
     *
     * @return ContextDefinitionInterface
     *
     * @throws RuntimeException
     */
    protected function setupContextDefinition(string $contextName, string $dispatchType)
    {
        $contextDefinition = $this->contextDefinitionBuilder->buildContextDefinition($contextName, $dispatchType);

        if (!$contextDefinition instanceof ContextDefinitionInterface) {
            throw new RuntimeException(sprintf('Context definition "%s" does not exist', $contextName));
        }

        return $contextDefinition;
    }

    /**
     * @param ContextDefinitionInterface          $contextDefinition
     * @param PreConfiguredIndexProviderInterface $indexProvider
     *
     * @throws \Exception
     */
    private function setupPreConfiguredIndexProvider(ContextDefinitionInterface $contextDefinition, PreConfiguredIndexProviderInterface $indexProvider)
    {
        try {
            $indexDocument = $this->indexDocumentGenerator->generateWithoutData($contextDefinition, ['preConfiguredIndexProvider' => true]);
        } catch (\Throwable $e) {
            throw new \Exception(sprintf(
                    '%s. (The current context index provider also requires pre-configured indices. Please make sure your document definition implements the "%s" interface)',
                    $e->getMessage(), PreConfiguredIndexProviderInterface::class
                )
            );
        }

        if (!$indexDocument->hasIndexFields()) {
            throw new \Exception(sprintf(
                    'No Index Document found. The current context index provider requires pre-configured indices. Please make sure your document definition implements the "%s" interface',
                    PreConfiguredIndexProviderInterface::class
                )
            );
        }

        $indexProvider->preConfigureIndex($indexDocument);
    }

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param array                      $providers
     *
     * @throws SilentException
     */
    protected function warmUpProvider(ContextDefinitionInterface $contextDefinition, array $providers)
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
                    $e->getMessage(), $e->getLine(), $e->getFile()
                ), $contextDefinition, $providers);

                throw new SilentException(sprintf('Error on warm up provider'));
            } catch (\Throwable $e) {
                $this->dispatchFailOverToProviders(sprintf(
                    'Error while warming up provider. Error was: %s [Line: %s, File %s]. FailOver has been initiated',
                    $e->getMessage(), $e->getLine(), $e->getFile()
                ), $contextDefinition, $providers);

                throw new SilentException(sprintf('Error on warm up provider'));
            }
        }
    }

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param array                      $providers
     *
     * @throws SilentException
     */
    protected function coolDownProvider(ContextDefinitionInterface $contextDefinition, array $providers)
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
                    $e->getMessage(), $e->getLine(), $e->getFile()
                ), $contextDefinition, $providers);

                throw new SilentException(sprintf('Error on cooling down provider'));
            }
        }
    }

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param mixed                      $class
     * @param string                     $method
     * @param array                      $arguments
     * @param array                      $involvedProviders
     *
     * @throws SilentException
     */
    protected function callSaveMethod(ContextDefinitionInterface $contextDefinition, $class, string $method, array $arguments, array $involvedProviders)
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
                $e->getMessage(), $e->getLine(), $e->getFile()
            ), $contextDefinition, $involvedProviders);

            throw new SilentException(sprintf('Error on calling save method'));
        }
    }

    /**
     * @param string                     $errorMessage
     * @param ContextDefinitionInterface $contextDefinition
     * @param array                      $providers
     */
    protected function dispatchCancelledProcessToProviders(string $errorMessage, ContextDefinitionInterface $contextDefinition, array $providers)
    {
        $this->logger->error($errorMessage, 'workflow', $contextDefinition->getName());

        foreach ($providers as $provider) {
            $providerName = $provider instanceof DataProviderInterface ? $contextDefinition->getDataProviderName() : $contextDefinition->getIndexProviderName();
            $this->logger->log('DEBUG', sprintf('executing provider cancelled shutdown'), $providerName, $contextDefinition->getName());

            try {
                $provider->cancelledShutdown($contextDefinition);
            } catch (\Throwable $e) {
                $this->logger->error(sprintf(
                    'Error while dispatching cancelled process. Error was: %s.',
                    $e->getMessage()), get_class($provider), $contextDefinition->getName()
                );
            }
        }
    }

    /**
     * @param string                     $errorMessage
     * @param ContextDefinitionInterface $contextDefinition
     * @param array                      $providers
     */
    protected function dispatchFailOverToProviders(string $errorMessage, ContextDefinitionInterface $contextDefinition, array $providers)
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
