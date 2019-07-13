<?php

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ProcessCancelledException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;

abstract class AbstractRunner
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

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
                $this->logger->error(sprintf('Error while dispatching cancelled process. Error was: %s.', $e->getMessage()), get_class($provider),
                    $contextData->getName());
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