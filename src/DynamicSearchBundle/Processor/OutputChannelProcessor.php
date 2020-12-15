<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Builder\ContextDefinitionBuilderInterface;
use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\EventDispatcher\OutputChannelModifierEventDispatcher;
use DynamicSearchBundle\Exception\NormalizerException;
use DynamicSearchBundle\Exception\OutputChannelException;
use DynamicSearchBundle\Factory\PaginatorFactoryInterface;
use DynamicSearchBundle\Filter\FilterStackWorker;
use DynamicSearchBundle\Manager\FilterDefinitionManagerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\Manager\NormalizerManagerInterface;
use DynamicSearchBundle\Manager\OutputChannelManagerInterface;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocator;
use DynamicSearchBundle\OutputChannel\Context\OutputChannelContextInterface;
use DynamicSearchBundle\OutputChannel\Context\SubOutputChannelContextInterface;
use DynamicSearchBundle\OutputChannel\MultiOutputChannelInterface;
use DynamicSearchBundle\OutputChannel\Context\OutputChannelContext;
use DynamicSearchBundle\OutputChannel\Context\SubOutputChannelContext;
use DynamicSearchBundle\OutputChannel\OutputChannelInterface;
use DynamicSearchBundle\OutputChannel\Query\MultiSearchContainer;
use DynamicSearchBundle\OutputChannel\Query\Result\RawResultInterface;
use DynamicSearchBundle\OutputChannel\Query\SearchContainer;
use DynamicSearchBundle\OutputChannel\Result\MultiOutputChannelResult;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelArrayResult;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelPaginatorResult;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsBuilderInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

class OutputChannelProcessor implements OutputChannelProcessorInterface
{
    /**
     * @var ContextDefinitionBuilderInterface
     */
    protected $contextDefinitionBuilder;

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
     * @param ContextDefinitionBuilderInterface $contextDefinitionBuilder
     * @param OutputChannelManagerInterface     $outputChannelManager
     * @param FilterDefinitionManagerInterface  $filterDefinitionManager
     * @param IndexManagerInterface             $indexManager
     * @param NormalizerManagerInterface        $normalizerManager
     * @param PaginatorFactoryInterface         $paginatorFactory
     */
    public function __construct(
        ContextDefinitionBuilderInterface $contextDefinitionBuilder,
        OutputChannelManagerInterface $outputChannelManager,
        FilterDefinitionManagerInterface $filterDefinitionManager,
        IndexManagerInterface $indexManager,
        NormalizerManagerInterface $normalizerManager,
        PaginatorFactoryInterface $paginatorFactory
    ) {
        $this->contextDefinitionBuilder = $contextDefinitionBuilder;
        $this->outputChannelManager = $outputChannelManager;
        $this->filterDefinitionManager = $filterDefinitionManager;
        $this->indexManager = $indexManager;
        $this->normalizerManager = $normalizerManager;
        $this->paginatorFactory = $paginatorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchOutputChannelQuery(string $contextName, string $outputChannelName)
    {
        $contextDefinition = $this->getContextDefinition($contextName, $outputChannelName);
        $indexProviderOptions = $contextDefinition->getIndexProviderOptions();

        $runtimeQueryProvider = $this->getOCRuntimeQueryProvider($contextDefinition, $outputChannelName);
        $runtimeOptionsBuilder = $this->getOCRuntimeOptionsBuilder($contextDefinition, $outputChannelName);
        $runtimeOptions = $runtimeOptionsBuilder->buildOptions(null);

        $outputChannelServiceName = $contextDefinition->getOutputChannelServiceName($outputChannelName);
        $outputChannelEnvironment = $contextDefinition->getOutputChannelEnvironment($outputChannelName);

        $outputChannelAllocator = new OutputChannelAllocator($outputChannelName, null, null);

        $context = new OutputChannelContext();
        $context->setContextDefinition($contextDefinition);
        $context->setRuntimeQueryProvider($runtimeQueryProvider);
        $context->setRuntimeOptions($runtimeOptions);
        $context->setIndexProviderOptions($indexProviderOptions);
        $context->setOutputChannelAllocator($outputChannelAllocator);
        $context->setOutputChannelServiceName($outputChannelServiceName);

        $eventDispatcher = new OutputChannelModifierEventDispatcher($this->outputChannelManager);
        $eventDispatcher->setOutputChannelContext($context);

        if ($outputChannelEnvironment['multiple'] === false) {
            return $this->buildSingle($contextDefinition, $context, $eventDispatcher);
        }

        return $this->buildMulti($contextDefinition, $context, $runtimeOptionsBuilder, $eventDispatcher, $outputChannelEnvironment['blocks']);
    }

    /**
     * @param ContextDefinitionInterface           $contextDefinition
     * @param OutputChannelContextInterface        $outputChannelContext
     * @param OutputChannelModifierEventDispatcher $eventDispatcher
     *
     * @return OutputChannelArrayResult|OutputChannelPaginatorResult
     *
     * @throws OutputChannelException
     */
    protected function buildSingle(
        ContextDefinitionInterface $contextDefinition,
        OutputChannelContextInterface $outputChannelContext,
        OutputChannelModifierEventDispatcher $eventDispatcher
    ) {
        $filterStackWorker = new FilterStackWorker($outputChannelContext, $this->filterDefinitionManager, $this->indexManager);
        $filterServiceStack = $filterStackWorker->generateFilterServiceStack($outputChannelContext, $eventDispatcher);

        $outputChannelName = $outputChannelContext->getOutputChannelAllocator()->getOutputChannelName();
        $outputChannelService = $this->prepareOutputChannelService($eventDispatcher, $outputChannelContext);

        try {
            $rawQuery = $outputChannelService->getQuery();
        } catch (\Throwable $e) {
            throw new OutputChannelException(
                $outputChannelName,
                sprintf('Error while calling query() on output channel: %s', $e->getMessage())
            );
        }

        $query = $filterStackWorker->enrichStackQuery($filterServiceStack, $rawQuery);

        try {
            $searchContainer = $outputChannelService->getResult(new SearchContainer($outputChannelName, $query));
        } catch (\Throwable $e) {
            throw new OutputChannelException(
                $outputChannelName,
                sprintf('Error while calling getResult() on output channel: %s', $e->getMessage())
            );
        }

        $filterBlocks = $filterStackWorker->buildStackViewVars($searchContainer, $filterServiceStack);

        return $this->buildResult($contextDefinition, $outputChannelContext, $searchContainer->getRawResult(), $filterBlocks);
    }

    /**
     * @param ContextDefinitionInterface           $contextDefinition
     * @param OutputChannelContextInterface        $outputChannelContext
     * @param RuntimeOptionsBuilderInterface       $runtimeOptionsBuilder
     * @param OutputChannelModifierEventDispatcher $eventDispatcher
     * @param array                                $outputChannelBlocks
     *
     * @return MultiOutputChannelResult
     *
     * @throws OutputChannelException
     */
    protected function buildMulti(
        ContextDefinitionInterface $contextDefinition,
        OutputChannelContextInterface $outputChannelContext,
        RuntimeOptionsBuilderInterface $runtimeOptionsBuilder,
        OutputChannelModifierEventDispatcher $eventDispatcher,
        array $outputChannelBlocks
    ) {
        $multiOutputChannelService = $this->prepareOutputChannelService($eventDispatcher, $outputChannelContext);
        if (!$multiOutputChannelService instanceof MultiOutputChannelInterface) {
            throw new OutputChannelException(
                $outputChannelContext->getOutputChannelAllocator()->getOutputChannelName(),
                sprintf('Multi output channel needs to be interface of "%s"', MultiOutputChannelInterface::class)
            );
        }

        $filters = [];
        $queries = [];
        $subContexts = [];

        // execute all sub output channels
        foreach ($outputChannelBlocks as $subOutputChannelIdentifier => $block) {
            $subOutputChannelName = $block['reference'];

            $subOutputChannelServiceName = $contextDefinition->getOutputChannelServiceName($subOutputChannelName);
            $parentOutputChannelName = $outputChannelContext->getOutputChannelAllocator()->getOutputChannelName();
            $subOutputChannelAllocator = new OutputChannelAllocator($subOutputChannelName, $parentOutputChannelName, $subOutputChannelIdentifier);

            $runtimeOptions = $runtimeOptionsBuilder->buildOptions($subOutputChannelIdentifier);

            $subOutputChannelContext = new SubOutputChannelContext($outputChannelContext);
            $subOutputChannelContext->setOutputChannelAllocator($subOutputChannelAllocator);
            $subOutputChannelContext->setOutputChannelServiceName($subOutputChannelServiceName);
            $subOutputChannelContext->setRuntimeOptions($runtimeOptions);

            $eventDispatcher->setOutputChannelContext($subOutputChannelContext);

            $filterStackWorker = new FilterStackWorker($subOutputChannelContext, $this->filterDefinitionManager, $this->indexManager);
            $filterServiceStack = $filterStackWorker->generateFilterServiceStack($subOutputChannelContext, $eventDispatcher);

            $outputChannelService = $this->prepareOutputChannelService($eventDispatcher, $subOutputChannelContext);

            try {
                $query = $filterStackWorker->enrichStackQuery($filterServiceStack, $outputChannelService->getQuery());
            } catch (\Throwable $e) {
                throw new OutputChannelException(
                    $parentOutputChannelName,
                    sprintf('Error while calling query() on sub output channel "%s": %s', $subOutputChannelIdentifier, $e->getMessage())
                );
            }

            $filters[$subOutputChannelIdentifier] = [$filterStackWorker, $filterServiceStack];
            $queries[$subOutputChannelIdentifier] = $query;
            $subContexts[$subOutputChannelIdentifier] = $subOutputChannelContext;
        }

        // reset multichannel context
        $eventDispatcher->setOutputChannelContext($outputChannelContext);

        // execute all sub query
        $multiSearchContainer = [];
        foreach ($outputChannelBlocks as $subOutputChannelIdentifier => $block) {
            $multiSearchContainer[] = new SearchContainer($subOutputChannelIdentifier, $queries[$subOutputChannelIdentifier]);
        }

        // fetch result of each sub query
        $results = [];
        foreach ($multiOutputChannelService->getMultiSearchResult(new MultiSearchContainer($multiSearchContainer)) as $searchContainer) {

            $subOutputChannelIdentifier = $searchContainer->getIdentifier();

            $filter = $filters[$subOutputChannelIdentifier];
            /** @var SubOutputChannelContextInterface $subOutputChannelContext */
            $subOutputChannelContext = $subContexts[$subOutputChannelIdentifier];

            if (!array_key_exists($subOutputChannelIdentifier, $outputChannelBlocks)) {
                throw new OutputChannelException(
                    $subOutputChannelContext->getOutputChannelAllocator()->getOutputChannelName(),
                    sprintf('Sub output channel with identifier "%s" not found after calling getMultiSearchResult.', $subOutputChannelIdentifier)
                );
            }

            /** @var FilterStackWorker $filterStackWorker */
            $filterStackWorker = $filter[0];
            /** @var array $filterServiceStack */
            $filterServiceStack = $filter[1];

            $filterBlocks = $filterStackWorker->buildStackViewVars($searchContainer, $filterServiceStack);

            $results[$subOutputChannelIdentifier] = $this->buildResult($contextDefinition, $subOutputChannelContext, $searchContainer->getRawResult(), $filterBlocks);
        }

        return new MultiOutputChannelResult($results, $outputChannelContext->getRuntimeQueryProvider());
    }

    /**
     * @param ContextDefinitionInterface    $contextDefinition
     * @param OutputChannelContextInterface $outputChannelContext
     * @param RawResultInterface            $rawResult
     * @param array                         $filterBlocks
     *
     * @return OutputChannelArrayResult|OutputChannelPaginatorResult
     *
     * @throws OutputChannelException
     */
    protected function buildResult(
        ContextDefinitionInterface $contextDefinition,
        OutputChannelContextInterface $outputChannelContext,
        RawResultInterface $rawResult,
        array $filterBlocks
    ) {
        $outputChannelName = $outputChannelContext->getOutputChannelAllocator()->getOutputChannelName();
        $runtimeOptions = $outputChannelContext->getRuntimeOptions();
        $runtimeQueryProvider = $outputChannelContext->getRuntimeQueryProvider();

        $documentNormalizer = $this->getDocumentNormalizer($contextDefinition, $outputChannelName);
        $paginatorOptions = $contextDefinition->getOutputChannelPaginatorOptions($outputChannelName);

        if ($paginatorOptions['enabled'] === true) {
            $paginator = $this->paginatorFactory->create(
                $paginatorOptions['adapter_class'],
                $outputChannelName,
                $rawResult,
                $contextDefinition,
                $documentNormalizer
            );

            $paginator->setItemCountPerPage($paginatorOptions['max_per_page']);
            $paginator->setCurrentPageNumber($runtimeOptions['current_page']);

            $paginatorOutputResult = new OutputChannelPaginatorResult(
                $contextDefinition->getName(),
                $rawResult->getHitCount(),
                $outputChannelContext->getOutputChannelAllocator(),
                $filterBlocks,
                $runtimeOptions,
                $runtimeQueryProvider
            );

            $paginatorOutputResult->setPaginator($paginator);

            return $paginatorOutputResult;
        }

        $result = [];
        if ($documentNormalizer instanceof DocumentNormalizerInterface) {
            try {
                $result = $documentNormalizer->normalize($rawResult, $contextDefinition, $outputChannelName);
            } catch (\Exception $e) {
                throw new OutputChannelException($outputChannelName, $e->getMessage(), $e);
            }
        }

        $arrayOutputResult = new OutputChannelArrayResult(
            $contextDefinition->getName(),
            $rawResult->getHitCount(),
            $outputChannelContext->getOutputChannelAllocator(),
            $filterBlocks,
            $runtimeOptions,
            $runtimeQueryProvider
        );

        $arrayOutputResult->setResult($result);

        return $arrayOutputResult;
    }

    /**
     * @param OutputChannelModifierEventDispatcher $eventDispatcher
     * @param OutputChannelContextInterface        $outputChannelContext
     *
     * @return OutputChannelInterface|null
     *
     * @throws OutputChannelException
     */
    protected function prepareOutputChannelService(OutputChannelModifierEventDispatcher $eventDispatcher, OutputChannelContextInterface $outputChannelContext)
    {
        $outputChannelName = $outputChannelContext->getOutputChannelAllocator()->getOutputChannelName();
        $contextDefinition = $outputChannelContext->getContextDefinition();

        try {
            $outputChannelService = $this->outputChannelManager->getOutputChannel($contextDefinition, $outputChannelName);
        } catch (\Throwable $e) {
            throw new OutputChannelException(
                $outputChannelName,
                sprintf(
                    'could not load output channel "%s" for context "%s". Error was: %s',
                    $outputChannelName,
                    $contextDefinition->getName(),
                    $e->getMessage()
                )
            );
        }

        if (!$outputChannelService instanceof OutputChannelInterface) {
            throw new OutputChannelException(
                $outputChannelName,
                sprintf('could not load output channel "%s" for context "%s"', $outputChannelName, $contextDefinition->getName())
            );
        }

        try {
            $outputChannelOptions = $contextDefinition->getOutputChannelOptions($outputChannelName);
        } catch (\Throwable $e) {
            throw new OutputChannelException(
                $outputChannelName,
                sprintf(
                    'could not determinate output channel options for "%s" for context "%s". Error was: %s',
                    $outputChannelName,
                    $contextDefinition->getName(),
                    $e->getMessage()
                )
            );
        }

        $outputChannelService->setOptions($outputChannelOptions);
        $outputChannelService->setEventDispatcher($eventDispatcher);
        $outputChannelService->setOutputChannelContext($outputChannelContext);

        return $outputChannelService;
    }

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param string                     $outputChannelName
     *
     * @return DocumentNormalizerInterface|null
     *
     * @throws OutputChannelException
     */
    protected function getDocumentNormalizer(ContextDefinitionInterface $contextDefinition, string $outputChannelName)
    {
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

        return $documentNormalizer;
    }

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param string                     $outputChannelName
     *
     * @return RuntimeQueryProviderInterface
     *
     * @throws OutputChannelException
     */
    protected function getOCRuntimeQueryProvider(ContextDefinitionInterface $contextDefinition, string $outputChannelName)
    {
        $outputChannelRuntimeQueryProviderName = $contextDefinition->getOutputChannelRuntimeQueryProvider($outputChannelName);
        $runtimeQueryProvider = $this->outputChannelManager->getOutputChannelRuntimeQueryProvider($outputChannelRuntimeQueryProviderName);
        if (!$runtimeQueryProvider instanceof RuntimeQueryProviderInterface) {
            throw new OutputChannelException(
                $outputChannelName,
                sprintf('could not load runtime query provider "%s" for context "%s"', $outputChannelRuntimeQueryProviderName, $contextDefinition->getName())
            );
        }

        return $runtimeQueryProvider;
    }

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param string                     $outputChannelName
     *
     * @return RuntimeOptionsBuilderInterface
     *
     * @throws OutputChannelException
     */
    protected function getOCRuntimeOptionsBuilder(ContextDefinitionInterface $contextDefinition, string $outputChannelName)
    {
        $outputChannelRuntimeOptionsBuilderName = $contextDefinition->getOutputChannelRuntimeOptionsBuilder($outputChannelName);
        $runtimeOptionsBuilder = $this->outputChannelManager->getOutputChannelRuntimeOptionsBuilder($outputChannelRuntimeOptionsBuilderName);
        if (!$runtimeOptionsBuilder instanceof RuntimeOptionsBuilderInterface) {
            throw new OutputChannelException(
                $outputChannelName,
                sprintf('could not load runtime options builder "%s" for context "%s"', $outputChannelRuntimeOptionsBuilderName, $contextDefinition->getName())
            );
        }

        return $runtimeOptionsBuilder;
    }

    /**
     * @param string $contextName
     * @param string $outputChannelName
     *
     * @return ContextDefinitionInterface
     *
     * @throws OutputChannelException
     */
    protected function getContextDefinition(string $contextName, string $outputChannelName)
    {
        $contextDefinition = $this->contextDefinitionBuilder->buildContextDefinition($contextName, ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_FETCH);
        if (!$contextDefinition instanceof ContextDefinitionInterface) {
            throw new OutputChannelException($outputChannelName, sprintf('could not load context data for context "%s"', $contextName));
        }

        return $contextDefinition;
    }
}
