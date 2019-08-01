<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
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
use DynamicSearchBundle\OutputChannel\Result\MultiOutputChannelResult;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelArrayResult;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelPaginatorResult;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsBuilderInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;
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
    public function dispatchOutputChannelQuery(string $contextName, string $outputChannelName)
    {
        $contextDefinition = $this->getContextDefinition($contextName, $outputChannelName);
        $indexProvider = $this->getIndexProvider($contextDefinition, $outputChannelName);
        $indexProviderOptions = $this->getIndexProviderOptions($contextDefinition, $outputChannelName, $indexProvider);

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
     * @param ContextDataInterface                 $contextDefinition
     * @param OutputChannelContextInterface        $outputChannelContext
     * @param OutputChannelModifierEventDispatcher $eventDispatcher
     *
     * @return OutputChannelArrayResult|OutputChannelPaginatorResult
     *
     * @throws OutputChannelException
     */
    protected function buildSingle(
        ContextDataInterface $contextDefinition,
        OutputChannelContextInterface $outputChannelContext,
        OutputChannelModifierEventDispatcher $eventDispatcher
    ) {
        $filterStackWorker = new FilterStackWorker($outputChannelContext, $this->filterDefinitionManager, $this->indexManager);
        $filterServiceStack = $filterStackWorker->generateFilterServiceStack($outputChannelContext, $eventDispatcher);

        $outputChannelService = $this->prepareOutputChannelService($eventDispatcher, $outputChannelContext);

        $query = $filterStackWorker->enrichStackQuery($filterServiceStack, $outputChannelService->getQuery());

        $result = $outputChannelService->getResult($query);

        $filterBlocks = $filterStackWorker->buildStackViewVars($filterServiceStack, $result, $query);

        return $this->buildResult($contextDefinition, $outputChannelContext, $filterBlocks, $result);
    }

    /**
     * @param ContextDataInterface                 $contextDefinition
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
        ContextDataInterface $contextDefinition,
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

        foreach ($outputChannelBlocks as $subOutputChannelIdentifier => $block) {

            $subOutputChannelName = $block['reference'];

            $parentOutputChannelName = $outputChannelContext->getOutputChannelAllocator()->getOutputChannelName();
            $subOutputChannelAllocator = new OutputChannelAllocator($subOutputChannelName, $parentOutputChannelName, $subOutputChannelIdentifier);

            $runtimeOptions = $runtimeOptionsBuilder->buildOptions($subOutputChannelIdentifier);

            $subOutputChannelContext = new SubOutputChannelContext($outputChannelContext);
            $subOutputChannelContext->setOutputChannelAllocator($subOutputChannelAllocator);
            $subOutputChannelContext->setRuntimeOptions($runtimeOptions);

            $eventDispatcher->setOutputChannelContext($subOutputChannelContext);

            $filterStackWorker = new FilterStackWorker($subOutputChannelContext, $this->filterDefinitionManager, $this->indexManager);
            $filterServiceStack = $filterStackWorker->generateFilterServiceStack($outputChannelContext, $eventDispatcher);

            $outputChannelService = $this->prepareOutputChannelService($eventDispatcher, $subOutputChannelContext);

            $query = $filterStackWorker->enrichStackQuery($filterServiceStack, $outputChannelService->getQuery());

            $multiOutputChannelService->addSubQuery($subOutputChannelIdentifier, $query);

            $filters[$subOutputChannelIdentifier] = [$filterStackWorker, $filterServiceStack];
            $queries[$subOutputChannelIdentifier] = $query;
            $subContexts[$subOutputChannelIdentifier] = $subOutputChannelContext;
        }

        $results = [];
        foreach ($multiOutputChannelService->getMultiSearchResult() as $subOutputChannelIdentifier => $result) {

            $filter = $filters[$subOutputChannelIdentifier];
            $query = $queries[$subOutputChannelIdentifier];
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
            /** @var array $filterStackWorker */
            $filterServiceStack = $filter[1];

            $filterBlocks = $filterStackWorker->buildStackViewVars($filterServiceStack, $result, $query);

            $results[$subOutputChannelIdentifier] = $this->buildResult($contextDefinition, $subOutputChannelContext, $filterBlocks, $result);

        }

        return new MultiOutputChannelResult($results, $outputChannelContext->getRuntimeQueryProvider());
    }

    /**
     * @param ContextDataInterface          $contextData
     * @param OutputChannelContextInterface $outputChannelContext
     * @param array                         $filterBlocks
     * @param mixed                         $result
     *
     * @return OutputChannelArrayResult|OutputChannelPaginatorResult
     *
     * @throws OutputChannelException
     */
    protected function buildResult(ContextDataInterface $contextData, OutputChannelContextInterface $outputChannelContext, array $filterBlocks, $result)
    {
        $outputChannelName = $outputChannelContext->getOutputChannelAllocator()->getOutputChannelName();
        $runtimeOptions = $outputChannelContext->getRuntimeOptions();
        $runtimeQueryProvider = $outputChannelContext->getRuntimeQueryProvider();

        $documentNormalizer = $this->getDocumentNormalizer($contextData, $outputChannelName);
        $paginatorOptions = $contextData->getOutputChannelPaginatorOptions($outputChannelName);

        if ($paginatorOptions['enabled'] === true) {
            $paginator = $this->paginatorFactory->create(
                $result,
                $paginatorOptions['adapter_class'],
                $outputChannelName,
                $contextData,
                $documentNormalizer
            );

            $paginator->setItemCountPerPage($paginatorOptions['max_per_page']);
            $paginator->setCurrentPageNumber($runtimeOptions['current_page']);

            $paginatorOutputResult = new OutputChannelPaginatorResult(
                $contextData->getName(),
                $outputChannelName,
                $filterBlocks,
                $runtimeOptions,
                $runtimeQueryProvider
            );

            $paginatorOutputResult->setPaginator($paginator);

            return $paginatorOutputResult;
        }

        if ($documentNormalizer instanceof DocumentNormalizerInterface) {
            try {
                $result = $documentNormalizer->normalize($contextData, $outputChannelName, $result);
            } catch (\Exception $e) {
                throw new OutputChannelException($outputChannelName, $e->getMessage(), $e);
            }
        }

        $arrayOutputResult = new OutputChannelArrayResult(
            $contextData->getName(),
            $outputChannelName,
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
            throw new OutputChannelException($outputChannelName,
                sprintf('could not load output channel "%s" for context "%s". Error was: %s', $outputChannelName,
                    $contextDefinition->getName(), $e->getMessage())
            );
        }

        if (!$outputChannelService instanceof OutputChannelInterface) {
            throw new OutputChannelException($outputChannelName,
                sprintf('could not load output channel "%s" for context "%s"', $outputChannelName, $contextDefinition->getName()));
        }

        $eventData = $eventDispatcher->dispatchAction('resolve_options', ['optionsResolver' => new OptionsResolver()]);
        $optionsResolver = $eventData->getParameter('optionsResolver');

        try {
            $outputChannelOptions = $contextDefinition->getOutputChannelOptions($outputChannelName, $outputChannelService, $optionsResolver);
        } catch (\Throwable $e) {
            throw new OutputChannelException($outputChannelName,
                sprintf('could not determinate output channel options for "%s" for context "%s". Error was: %s',
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
     * @param ContextDataInterface $contextDefinition
     * @param string               $outputChannelName
     *
     * @return DocumentNormalizerInterface|null
     *
     * @throws OutputChannelException
     */
    protected function getDocumentNormalizer(ContextDataInterface $contextDefinition, string $outputChannelName)
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
     * @param ContextDataInterface $contextDefinition
     * @param string               $outputChannelName
     *
     * @return RuntimeQueryProviderInterface
     *
     * @throws OutputChannelException
     */
    protected function getOCRuntimeQueryProvider(ContextDataInterface $contextDefinition, string $outputChannelName)
    {
        $outputChannelRuntimeQueryProviderName = $contextDefinition->getOutputChannelRuntimeQueryProvider($outputChannelName);
        $runtimeQueryProvider = $this->outputChannelManager->getOutputChannelRuntimeQueryProvider($outputChannelRuntimeQueryProviderName);
        if (!$runtimeQueryProvider instanceof RuntimeQueryProviderInterface) {
            throw new OutputChannelException($outputChannelName,
                sprintf('could not load runtime query provider "%s" for context "%s"', $outputChannelRuntimeQueryProviderName, $contextDefinition->getName()));
        }

        return $runtimeQueryProvider;
    }

    /**
     * @param ContextDataInterface $contextDefinition
     * @param string               $outputChannelName
     *
     * @return RuntimeOptionsBuilderInterface
     *
     * @throws OutputChannelException
     */
    protected function getOCRuntimeOptionsBuilder(ContextDataInterface $contextDefinition, string $outputChannelName)
    {
        $outputChannelRuntimeOptionsBuilderName = $contextDefinition->getOutputChannelRuntimeOptionsBuilder($outputChannelName);
        $runtimeOptionsBuilder = $this->outputChannelManager->getOutputChannelRuntimeOptionsBuilder($outputChannelRuntimeOptionsBuilderName);
        if (!$runtimeOptionsBuilder instanceof RuntimeOptionsBuilderInterface) {
            throw new OutputChannelException($outputChannelName,
                sprintf('could not load runtime options builder "%s" for context "%s"', $outputChannelRuntimeOptionsBuilderName, $contextDefinition->getName()));
        }

        return $runtimeOptionsBuilder;
    }

    /**
     * @param ContextDataInterface   $contextDefinition
     * @param string                 $outputChannelName
     * @param IndexProviderInterface $indexProvider
     *
     * @return array
     *
     * @throws OutputChannelException
     */
    protected function getIndexProviderOptions(ContextDataInterface $contextDefinition, string $outputChannelName, IndexProviderInterface $indexProvider)
    {
        try {
            $indexProviderOptions = $contextDefinition->getIndexProviderOptions($indexProvider);
        } catch (\Throwable $e) {
            throw new OutputChannelException(
                $outputChannelName,
                sprintf(
                    'could not determinate index provider options for "%s" for context "%s". Error was: %s',
                    $contextDefinition->getIndexProviderName(),
                    $contextDefinition->getName(),
                    $e->getMessage()
                )
            );
        }

        return $indexProviderOptions;
    }

    /**
     * @param ContextDataInterface $contextDefinition
     * @param string               $outputChannelName
     *
     * @return IndexProviderInterface
     *
     * @throws OutputChannelException
     */
    protected function getIndexProvider(ContextDataInterface $contextDefinition, string $outputChannelName)
    {
        try {
            $indexProvider = $this->indexManager->getIndexProvider($contextDefinition);
        } catch (\Throwable $e) {
            throw new OutputChannelException(
                $outputChannelName,
                sprintf('could not load index manager "%s" for context "%s". Error was: %s', $contextDefinition->getIndexProviderName(), $contextDefinition->getName(),
                    $e->getMessage())
            );
        }

        if (!$indexProvider instanceof IndexProviderInterface) {
            throw new OutputChannelException(
                $outputChannelName,
                sprintf('could not load index manager "%s" for context "%s"', $contextDefinition->getIndexProviderName(), $contextDefinition->getName())
            );
        }

        return $indexProvider;
    }

    /**
     * @param string $contextName
     * @param string $outputChannelName
     *
     * @return ContextDataInterface
     *
     * @throws OutputChannelException
     */
    protected function getContextDefinition(string $contextName, string $outputChannelName)
    {
        $contextDefinition = $this->configuration->getContextDefinition(ContextDataInterface::CONTEXT_DISPATCH_TYPE_FETCH, $contextName);
        if (!$contextDefinition instanceof ContextDataInterface) {
            throw new OutputChannelException($outputChannelName, sprintf('could not load context data for context "%s"', $contextName));
        }

        return $contextDefinition;
    }
}
