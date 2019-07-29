<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\EventDispatcher\OutputChannelModifierEventDispatcher;
use DynamicSearchBundle\Exception\NormalizerException;
use DynamicSearchBundle\Exception\OutputChannelException;
use DynamicSearchBundle\Factory\PaginatorFactoryInterface;
use DynamicSearchBundle\Filter\Definition\FilterDefinition;
use DynamicSearchBundle\Filter\Definition\FilterDefinitionInterface;
use DynamicSearchBundle\Filter\FilterInterface;
use DynamicSearchBundle\Manager\FilterDefinitionManagerInterface;
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

class OutputChannelProcessor implements OutputChannelProcessorInterface
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
     * @var FilterDefinitionManagerInterface
     */
    protected $filterDefinitionManager;

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
     * @param ConfigurationInterface           $configuration
     * @param OutputChannelManagerInterface    $outputChannelManager
     * @param FilterDefinitionManagerInterface $filterDefinitionManager
     * @param IndexManagerInterface            $indexManager
     * @param NormalizerManagerInterface       $normalizerManager
     * @param PaginatorFactoryInterface        $paginatorFactory
     */
    public function __construct(
        ConfigurationInterface $configuration,
        OutputChannelManagerInterface $outputChannelManager,
        FilterDefinitionManagerInterface $filterDefinitionManager,
        IndexManagerInterface $indexManager,
        NormalizerManagerInterface $normalizerManager,
        PaginatorFactoryInterface $paginatorFactory
    ) {
        $this->configuration = $configuration;
        $this->outputChannelManager = $outputChannelManager;
        $this->filterDefinitionManager = $filterDefinitionManager;
        $this->indexManager = $indexManager;
        $this->normalizerManager = $normalizerManager;
        $this->paginatorFactory = $paginatorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchOutputChannelQuery(string $contextName, string $outputChannelName): OutputChannelResultInterface
    {
        $contextDefinition = $this->configuration->getContextDefinition(ContextDataInterface::CONTEXT_DISPATCH_TYPE_FETCH, $contextName);
        if (!$contextDefinition instanceof ContextDataInterface) {
            throw new OutputChannelException($outputChannelName, sprintf('could not load context data for context "%s"', $contextName));
        }

        try {
            $indexProvider = $this->indexManager->getIndexProvider($contextDefinition);
        } catch (\Throwable $e) {
            throw new OutputChannelException(
                $outputChannelName,
                sprintf('could not load index manager "%s" for context "%s". Error was: %s', $contextDefinition->getIndexProviderName(), $contextName, $e->getMessage())
            );
        }

        if (!$indexProvider instanceof IndexProviderInterface) {
            throw new OutputChannelException(
                $outputChannelName,
                sprintf('could not load index manager "%s" for context "%s"', $contextDefinition->getIndexProviderName(), $contextName)
            );
        }

        try {
            $outputChannelService = $this->outputChannelManager->getOutputChannel($contextDefinition, $outputChannelName);
        } catch (\Throwable $e) {
            throw new OutputChannelException(
                $outputChannelName,
                sprintf('could not load output channel "%s" for context "%s". Error was: %s', $outputChannelName, $contextName, $e->getMessage())
            );
        }

        if (!$outputChannelService instanceof OutputChannelInterface) {
            throw new OutputChannelException($outputChannelName, sprintf('could not load output channel "%s" for context "%s"', $outputChannelName, $contextName));
        }

        $outputChannelRuntimeOptionsProviderName = $contextDefinition->getOutputChannelRuntimeOptionsProvider($outputChannelName);
        $runtimeOptionsProvider = $this->outputChannelManager->getOutputChannelRuntimeOptionsProvider($outputChannelRuntimeOptionsProviderName);
        if (!$runtimeOptionsProvider instanceof RuntimeOptionsProviderInterface) {
            throw new OutputChannelException($outputChannelName, sprintf('could not load runtime options provider for context "%s"', $contextName));
        }

        $outputChannelServiceName = $contextDefinition->getOutputChannelServiceName($outputChannelName);

        $eventDispatcher = new OutputChannelModifierEventDispatcher($outputChannelServiceName, $outputChannelName, $contextDefinition, $this->outputChannelManager);
        $eventData = $eventDispatcher->dispatchAction('resolve_options', ['optionsResolver' => new OptionsResolver()]);

        try {
            $indexProviderOptions = $contextDefinition->getIndexProviderOptions($indexProvider);
        } catch (\Throwable $e) {
            throw new OutputChannelException(
                $outputChannelName,
                sprintf(
                    'could not determinate index provider options for "%s" for context "%s". Error was: %s',
                    $contextDefinition->getIndexProviderName(),
                    $contextName,
                    $e->getMessage()
                )
            );
        }

        try {
            $outputChannelOptions = $contextDefinition->getOutputChannelOptions($outputChannelName, $outputChannelService, $eventData->getParameter('optionsResolver'));
            $runtimeOptionsProvider->setDefaultOptions($outputChannelOptions);
        } catch (\Throwable $e) {
            throw new OutputChannelException(
                $outputChannelName,
                sprintf('could not determinate output channel options for "%s" for context "%s". Error was: %s', $outputChannelName, $contextName, $e->getMessage())
            );
        }

        try {
            $documentNormalizer = $this->normalizerManager->getDocumentNormalizerForOutputChannel($contextDefinition, $outputChannelName);
        } catch (NormalizerException $e) {
            throw new OutputChannelException(
                $outputChannelName,
                sprintf(
                    'Unable to load resource normalizer "%s". Error was: Error was: %s',
                    $contextDefinition->getOutputChannelNormalizerName($outputChannelName),
                    $e->getMessage()
                )
            );
        }

        $outputChannelService->setOptions($outputChannelOptions);
        $outputChannelService->setEventDispatcher($eventDispatcher);
        $outputChannelService->setRuntimeParameterProvider($runtimeOptionsProvider);
        $outputChannelService->setIndexProviderOptions($indexProviderOptions);

        $query = $outputChannelService->getQuery();

        $filterServices = [];

        try {
            $filterDefinition = $this->filterDefinitionManager->generateFilterDefinition($contextDefinition, $outputChannelName);
        } catch (\Throwable $e) {
            throw new OutputChannelException(
                $outputChannelName,
                sprintf('Unable to resolve filter definition. Error was: %s', $e->getMessage())
            );
        }

        if ($filterDefinition instanceof FilterDefinitionInterface) {
            $filterServices = $this->getFilterServices(
                $outputChannelName,
                $filterDefinition,
                $contextDefinition,
                $eventDispatcher,
                $runtimeOptionsProvider,
                $indexProviderOptions
            );
        }

        foreach ($filterServices as $filterService) {
            $query = $filterService->enrichQuery($query);
        }

        $result = $outputChannelService->getResult($query);

        $filterBlocks = [];
        foreach ($filterServices as $filterService) {
            if ($filterService->supportsFrontendView() === true) {
                $viewVars = $filterService->buildViewVars($query, $result);
                if ($viewVars !== null) {
                    $filterBlocks[] = $viewVars;
                }
            }
        }

        $hits = $outputChannelService->getHits($result);

        if ($outputChannelOptions['paginator']['enabled'] === true) {
            $paginator = $this->paginatorFactory->create(
                $hits,
                $outputChannelOptions['paginator']['adapter_class'],
                $outputChannelName,
                $contextDefinition,
                $documentNormalizer
            );

            $paginator->setItemCountPerPage($runtimeOptionsProvider->getMaxPerPage());
            $paginator->setCurrentPageNumber($runtimeOptionsProvider->getCurrentPage());

            return new OutputChannelPaginatorResult(
                $contextName,
                $outputChannelName,
                $filterBlocks,
                $runtimeOptionsProvider,
                $paginator
            );
        }

        if ($documentNormalizer instanceof DocumentNormalizerInterface) {
            try {
                $hits = $documentNormalizer->normalize($contextDefinition, $outputChannelName, $hits);
            } catch (\Exception $e) {
                throw new OutputChannelException($outputChannelName, $e->getMessage(), $e);
            }
        }

        return new OutputChannelArrayResult(
            $contextName,
            $outputChannelName,
            $filterBlocks,
            $runtimeOptionsProvider,
            $hits
        );
    }

    /**
     * @param string                               $outputChannelName
     * @param FilterDefinition                     $filterDefinition
     * @param ContextDataInterface                 $contextData
     * @param OutputChannelModifierEventDispatcher $eventDispatcher
     * @param RuntimeOptionsProviderInterface      $runtimeOptionsProvider
     * @param array                                $indexProviderOptions
     *
     * @return array|FilterInterface[]
     *
     * @throws OutputChannelException
     */
    protected function getFilterServices(
        string $outputChannelName,
        FilterDefinition $filterDefinition,
        ContextDataInterface $contextData,
        OutputChannelModifierEventDispatcher $eventDispatcher,
        RuntimeOptionsProviderInterface $runtimeOptionsProvider,
        array $indexProviderOptions
    ) {
        $filterServices = [];
        foreach ($filterDefinition->getFilterDefinitions() as $filterDefinition) {

            $filterTypeName = $filterDefinition['type'];
            $filterTypeConfiguration = $filterDefinition['configuration'];

            try {
                $filter = clone $this->indexManager->getFilter($contextData, $filterTypeName, $filterTypeConfiguration);
            } catch (\Throwable $e) {
                throw new OutputChannelException(
                    $outputChannelName,
                    sprintf(
                        'Unable to fetch filter "%s". Error was: %s',
                        $filterTypeName, $e->getMessage()
                    )
                );
            }

            if (!$filter instanceof FilterInterface) {
                continue;
            }

            $filter->setEventDispatcher($eventDispatcher);
            $filter->setRuntimeParameterProvider($runtimeOptionsProvider);
            $filter->setIndexProviderOptions($indexProviderOptions);

            $filterServices[] = $filter;
        }

        return $filterServices;
    }
}
