<?php

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ProcessCancelledException;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Exception\RuntimeException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\DataManagerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;

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
     * @var DataManagerInterface
     */
    protected $dataManager;

    /**
     * @var IndexManagerInterface
     */
    protected $indexManager;

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
     * @param ContextDataInterface $contextDefinition
     * @param string               $dataProviderBehaviour
     *
     * @return array
     * @throws RuntimeException
     */
    protected function setupProviders(ContextDataInterface $contextDefinition, string $dataProviderBehaviour)
    {
        return [
            'dataProvider'  => $this->setupDataProvider($contextDefinition, $dataProviderBehaviour),
            'indexProvider' => $this->setupIndexProvider($contextDefinition)
        ];
    }

    /**
     * @param ContextDataInterface $contextDefinition
     *
     * @return IndexProviderInterface
     */
    protected function setupIndexProvider(ContextDataInterface $contextDefinition)
    {
        try {
            $indexProvider = $this->indexManager->getIndexProvider($contextDefinition);
        } catch (ProviderException $e) {
            throw new RuntimeException(sprintf('Error while fetching provider. Error was: %s.', $e->getMessage()));
        }

        return $indexProvider;
    }

    /**
     * @param ContextDataInterface $contextDefinition
     * @param string               $providerBehaviour
     *
     * @return DataProviderInterface
     */
    protected function setupDataProvider(ContextDataInterface $contextDefinition, string $providerBehaviour = DataProviderInterface::PROVIDER_BEHAVIOUR_FULL_DISPATCH)
    {
        try {
            $dataProvider = $this->dataManager->getDataProvider($contextDefinition, $providerBehaviour);
        } catch (ProviderException $e) {
            throw new RuntimeException(sprintf('Error while fetching provider. Error was: %s.', $e->getMessage()));
        }

        return $dataProvider;
    }

    /**
     * @param string $contextName
     * @param string $dispatchType
     *
     * @return ContextDataInterface
     * @throws RuntimeException
     */
    protected function setupContextDefinition(string $contextName, string $dispatchType)
    {
        $contextDefinition = $this->configuration->getContextDefinition($dispatchType, $contextName);

        if (!$contextDefinition instanceof ContextDataInterface) {
            throw new RuntimeException(sprintf('Context configuration "%s" does not exist', $contextName));
        }

        return $contextDefinition;
    }

    /**
     * @param ContextDataInterface $contextData
     * @param array                $providers
     */
    protected function warmUpProvider(ContextDataInterface $contextData, array $providers)
    {
        foreach ($providers as $provider) {
            $providerName = $provider instanceof DataProviderInterface ? $contextData->getDataProviderName() : $contextData->getIndexProviderName();
            $this->logger->log('DEBUG', sprintf('warm up provider'), $providerName, $contextData->getName());

            try {
                $provider->warmUp($contextData);
            } catch (ProcessCancelledException $e) {
                $errorMessage = sprintf('Process has been cancelled. Message was: %s. Process canceling has been initiated', $e->getMessage());
                $this->dispatchCancelledProcessToProviders($errorMessage, $contextData, $providers);
            } catch (\Throwable $e) {
                $errorMessage = sprintf('Error while warming up provider. Error was: %s. FailOver has been initiated', $e->getMessage());
                $this->dispatchFailOverToProviders($errorMessage, $contextData, $providers);
            }
        }
    }

    /**
     * @param ContextDataInterface $contextData
     * @param array                $providers
     */
    protected function coolDownProvider(ContextDataInterface $contextData, array $providers)
    {
        foreach ($providers as $provider) {
            $providerName = $provider instanceof DataProviderInterface ? $contextData->getDataProviderName() : $contextData->getIndexProviderName();
            $this->logger->log('DEBUG', sprintf('cooling down provider'), $providerName, $contextData->getName());

            try {
                $provider->coolDown($contextData);
            } catch (ProcessCancelledException $e) {
                $errorMessage = sprintf('Process has been cancelled. Message was: %s. Process canceling has been initiated', $e->getMessage());
                $this->dispatchCancelledProcessToProviders($errorMessage, $contextData, $providers);
            } catch (\Throwable $e) {
                $errorMessage = sprintf('Error while cooling down provider. Error was: %s. FailOver has been initiated', $e->getMessage());
                $this->dispatchFailOverToProviders($errorMessage, $contextData, $providers);
            }
        }
    }

    /**
     * @param ContextDataInterface $contextData
     * @param mixed                $class
     * @param string               $method
     * @param array                $arguments
     * @param array                $involvedProviders
     */
    protected function callSaveMethod(ContextDataInterface $contextData, $class, string $method, array $arguments, array $involvedProviders)
    {
        try {
            call_user_func_array([$class, $method], $arguments);
        } catch (ProcessCancelledException $e) {
            $errorMessage = sprintf('Process has been cancelled. Message was: %s. Process canceling has been initiated', $e->getMessage());
            $this->dispatchCancelledProcessToProviders($errorMessage, $contextData, $involvedProviders);
        } catch (\Throwable $e) {
            $errorMessage = sprintf('Error while executing data provider. Error was: %s. FailOver has been initiated', $e->getMessage());
            $this->dispatchFailOverToProviders($errorMessage, $contextData, $involvedProviders);
        }
    }

    /**
     * @param string               $errorMessage
     * @param ContextDataInterface $contextData
     * @param array                $providers
     */
    protected function dispatchCancelledProcessToProviders(string $errorMessage, ContextDataInterface $contextData, array $providers)
    {
        $this->logger->error($errorMessage, 'workflow', $contextData->getName());

        foreach ($providers as $provider) {
            $providerName = $provider instanceof DataProviderInterface ? $contextData->getDataProviderName() : $contextData->getIndexProviderName();
            $this->logger->log('DEBUG', sprintf('executing provider cancelled shutdown'), $providerName, $contextData->getName());

            try {
                $provider->cancelledShutdown($contextData);
            } catch (\Throwable $e) {
                $this->logger->error(
                    sprintf('Error while dispatching cancelled process. Error was: %s.', $e->getMessage()),
                    get_class($provider),
                    $contextData->getName()
                );
            }
        }
    }

    /**
     * @param string               $errorMessage
     * @param ContextDataInterface $contextData
     * @param array                $providers
     */
    protected function dispatchFailOverToProviders(string $errorMessage, ContextDataInterface $contextData, array $providers)
    {
        $this->logger->error($errorMessage, 'workflow', $contextData->getName());

        foreach ($providers as $provider) {
            $providerName = $provider instanceof DataProviderInterface ? $contextData->getDataProviderName() : $contextData->getIndexProviderName();
            $this->logger->log('DEBUG', sprintf('executing provider emergency shutdown'), $providerName, $contextData->getName());

            try {
                $provider->emergencyShutdown($contextData);
            } catch (\Throwable $e) {
                $this->logger->error(sprintf('Error while dispatching fail over. Error was: %s.', $e->getMessage()), 'workflow', $contextData->getName());
            }
        }
    }
}
