<?php

namespace DynamicSearchBundle\Processor\SubProcessor;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\EventDispatcher\OutputChannelModifierEventDispatcher;
use DynamicSearchBundle\Exception\NormalizerException;
use DynamicSearchBundle\Exception\OutputChannelException;
use DynamicSearchBundle\Factory\PaginatorFactoryInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\Manager\NormalizerManagerInterface;
use DynamicSearchBundle\Manager\OutputChannelManagerInterface;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\OutputChannel\OutputChannelInterface;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelArrayResult;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelPaginatorResult;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelResultInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsProviderInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutputChannelSubProcessor implements OutputChannelSubProcessorInterface
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var OutputChannelManagerInterface
     */
    protected $outputChannelManager;

    /**
     * @var IndexManagerInterface
     */
    protected $indexManager;

    /**
     * @var NormalizerManagerInterface
     */
    protected $normalizerManager;

    /**
     * @var PaginatorFactoryInterface
     */
    protected $paginatorFactory;

    /**
     * @param ConfigurationInterface        $configuration
     * @param OutputChannelManagerInterface $outputChannelManager
     * @param IndexManagerInterface         $indexManager
     * @param NormalizerManagerInterface    $normalizerManager
     * @param PaginatorFactoryInterface     $paginatorFactory
     */
    public function __construct(
        ConfigurationInterface $configuration,
        OutputChannelManagerInterface $outputChannelManager,
        IndexManagerInterface $indexManager,
        NormalizerManagerInterface $normalizerManager,
        PaginatorFactoryInterface $paginatorFactory
    ) {
        $this->configuration = $configuration;
        $this->outputChannelManager = $outputChannelManager;
        $this->indexManager = $indexManager;
        $this->normalizerManager = $normalizerManager;
        $this->paginatorFactory = $paginatorFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function dispatchOutputChannelQuery(string $contextName, string $outputChannelName, $options = []): OutputChannelResultInterface
    {
        $contextDefinition = $this->configuration->getContextDefinition(ContextDataInterface::CONTEXT_DISPATCH_TYPE_FETCH, $contextName);
        if (!$contextDefinition instanceof ContextDataInterface) {
            throw new OutputChannelException($outputChannelName, sprintf('could not load context data for context "%s"', $contextName));
        }

        try {
            $indexProvider = $this->indexManager->getIndexProvider($contextDefinition);
        } catch (\Throwable $e) {
            throw new OutputChannelException($outputChannelName,
                sprintf('could not load index manager "%s" for context "%s". Error was: %s', $contextDefinition->getIndexProviderName(), $contextName, $e->getMessage())
            );
        }

        if (!$indexProvider instanceof IndexProviderInterface) {
            throw new OutputChannelException($outputChannelName,
                sprintf('could not load index manager "%s" for context "%s"', $contextDefinition->getIndexProviderName(), $contextName));
        }

        try {
            $outputChannelService = $this->outputChannelManager->getOutputChannel($contextDefinition, $outputChannelName);
        } catch (\Throwable $e) {
            throw new OutputChannelException($outputChannelName,
                sprintf('could not load output channel "%s" for context "%s". Error was: %s', $outputChannelName, $contextName, $e->getMessage())
            );
        }

        if (!$outputChannelService instanceof OutputChannelInterface) {
            throw new OutputChannelException($outputChannelName, sprintf('could not load output channel "%s" for context "%s"', $outputChannelName, $contextName));
        }

        $outputChannelRuntimeOptionsProviderName = $contextDefinition->getOutputChannelRuntimeOptionsProvider($outputChannelName);
        $outputChannelRuntimeOptionsProvider = $this->outputChannelManager->getOutputChannelRuntimeOptionsProvider($outputChannelRuntimeOptionsProviderName);
        if (!$outputChannelRuntimeOptionsProvider instanceof RuntimeOptionsProviderInterface) {
            throw new OutputChannelException($outputChannelName, sprintf('could not load runtime options provider for context "%s"', $contextName));
        }

        $outputChannelServiceName = $contextDefinition->getOutputChannelServiceName($outputChannelName);

        $eventDispatcher = new OutputChannelModifierEventDispatcher($outputChannelServiceName, $outputChannelName, $contextDefinition, $this->outputChannelManager);
        $eventData = $eventDispatcher->dispatchAction('resolve_options', ['optionsResolver' => new OptionsResolver()]);

        try {
            $indexProviderOptions = $contextDefinition->getIndexProviderOptions($indexProvider);
        } catch (\Throwable $e) {
            throw new OutputChannelException($outputChannelName,
                sprintf('could not determinate index provider options for "%s" for context "%s". Error was: %s',
                    $contextDefinition->getIndexProviderName(),
                    $contextName,
                    $e->getMessage()
                )
            );
        }

        try {
            $outputChannelOptions = $contextDefinition->getOutputChannelOptions($outputChannelName, $outputChannelService, $eventData->getParameter('optionsResolver'));
            $outputChannelRuntimeOptionsProvider->setDefaultOptions($outputChannelOptions);
        } catch (\Throwable $e) {
            throw new OutputChannelException($outputChannelName,
                sprintf('could not determinate output channel options for "%s" for context "%s". Error was: %s', $outputChannelName, $contextName, $e->getMessage())
            );
        }

        try {
            $documentNormalizer = $this->normalizerManager->getDocumentNormalizerForOutputChannel($contextDefinition, $outputChannelName);
        } catch (NormalizerException $e) {
            throw new OutputChannelException($outputChannelName,
                sprintf('Unable to load resource normalizer "%s". Error was: Error was: %s',
                    $contextDefinition->getOutputChannelNormalizerName($outputChannelName),
                    $e->getMessage()
                )
            );
        }

        $outputChannelService->setEventDispatcher($eventDispatcher);
        $outputChannelService->setRuntimeParameterProvider($outputChannelRuntimeOptionsProvider);

        $result = $outputChannelService->execute($indexProviderOptions, $outputChannelOptions, $options);

        if ($outputChannelOptions['paginator']['enabled'] === true) {

            $paginator = $this->paginatorFactory->create(
                $result,
                $outputChannelOptions['paginator']['adapter_class'],
                $outputChannelName,
                $contextDefinition,
                $documentNormalizer
            );

            $paginator->setItemCountPerPage($outputChannelRuntimeOptionsProvider->getMaxPerPage());
            $paginator->setCurrentPageNumber($outputChannelRuntimeOptionsProvider->getCurrentPage());

            return new OutputChannelPaginatorResult(
                $contextName,
                $outputChannelName,
                $outputChannelRuntimeOptionsProvider,
                $paginator
            );
        }

        if ($documentNormalizer instanceof DocumentNormalizerInterface) {
            try {
                $result = $documentNormalizer->normalize($contextDefinition, $outputChannelName, $result);
            } catch (\Exception $e) {
                throw new OutputChannelException($outputChannelName, $e->getMessage(), $e);
            }
        }

        return new OutputChannelArrayResult(
            $contextName,
            $outputChannelName,
            $outputChannelRuntimeOptionsProvider,
            $result
        );
    }
}
