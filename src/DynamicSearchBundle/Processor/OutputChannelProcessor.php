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
use DynamicSearchBundle\OutputChannel\Context\OutputChannelContextInterface;
use DynamicSearchBundle\OutputChannel\Context\SubOutputChannelContextInterface;
use DynamicSearchBundle\OutputChannel\MultiOutputChannelInterface;
use DynamicSearchBundle\OutputChannel\Context\OutputChannelContext;
use DynamicSearchBundle\OutputChannel\Context\SubOutputChannelContext;
use DynamicSearchBundle\OutputChannel\OutputChannelInterface;
use DynamicSearchBundle\OutputChannel\Result\MultiOutputChannelResult;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelArrayResult;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelPaginatorResult;
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
    public function dispatchOutputChannelQuery(string $contextName, string $outputChannelName)
    {
        $contextDefinition = $this->getContextDefinition($contextName, $outputChannelName);
        $indexProvider = $this->getIndexProvider($contextDefinition, $outputChannelName);
        $indexProviderOptions = $this->getIndexProviderOptions($contextDefinition, $outputChannelName, $indexProvider);

        $runtimeOptionsProvider = $this->getOCRuntimeOptionsProvider($contextDefinition, $outputChannelName);

        $outputChannelServiceName = $contextDefinition->getOutputChannelServiceName($outputChannelName);
        $outputChannelEnvironment = $contextDefinition->getOutputChannelEnvironment($outputChannelName);

        $context = new OutputChannelContext();
        $context->setContextDefinition($contextDefinition);
        $context->setRuntimeOptionsProvider($runtimeOptionsProvider);
        $context->setIndexProviderOptions($indexProviderOptions);
        $context->setOutputChannelName($outputChannelName);
        $context->setOutputChannelServiceName($outputChannelServiceName);

        $eventDispatcher = new OutputChannelModifierEventDispatcher($this->outputChannelManager);
        $eventDispatcher->setOutputChannelContext($context);

        if ($outputChannelEnvironment['multiple'] === false) {

            $filterStackWorker = new FilterStackWorker($context, $this->filterDefinitionManager, $this->indexManager);
            $filterServiceStack = $filterStackWorker->generateFilterServiceStack($context, $eventDispatcher);

            $outputChannelService = $this->prepareOutputChannelService($eventDispatcher, $context);

            $query = $outputChannelService->getQuery();
            $query = $filterStackWorker->enrichStackQuery($filterServiceStack, $query);

            $result = $outputChannelService->getResult($query);

            $filterBlocks = $filterStackWorker->buildStackViewVars($filterServiceStack, $result, $query);

            return $this->parseResult(
                $contextDefinition,
                $runtimeOptionsProvider,
                $outputChannelName,
                $outputChannelService->getOptions(),
                $filterBlocks,
                $result
            );

        }

        $multiOutputChannelService = $this->prepareOutputChannelService($eventDispatcher, $context);
        if (!$multiOutputChannelService instanceof MultiOutputChannelInterface) {
            throw new OutputChannelException(
                $outputChannelName,
                sprintf('Multi output channel needs to be interface of "%s"', MultiOutputChannelInterface::class)
            );
        }

        $filters = [];
        $queries = [];
        $outputChannelOptions = [];
        $subOutputChanelNames = [];

        foreach ($outputChannelEnvironment['blocks'] as $subOutputChannelIdentifier => $block) {

            $outputChannelName = $block['reference'];

            $subContext = new SubOutputChannelContext($context);
            $subContext->setParentOutputChannelName($context->getOutputChannelName());
            $subContext->setOutputChannelName($outputChannelName);

            $eventDispatcher->setOutputChannelContext($subContext);

            $filterStackWorker = new FilterStackWorker($subContext, $this->filterDefinitionManager, $this->indexManager);
            $filterServiceStack = $filterStackWorker->generateFilterServiceStack($context, $eventDispatcher);

            $outputChannelService = $this->prepareOutputChannelService($eventDispatcher, $subContext);

            $query = $outputChannelService->getQuery();
            $query = $filterStackWorker->enrichStackQuery($filterServiceStack, $query);

            $multiOutputChannelService->addSubQuery($subOutputChannelIdentifier, $query);

            $filters[$subOutputChannelIdentifier] = [$filterStackWorker, $filterServiceStack];
            $queries[$subOutputChannelIdentifier] = $query;
            $outputChannelOptions[$subOutputChannelIdentifier] = $outputChannelService->getOptions();
            $subOutputChanelNames[$subOutputChannelIdentifier] = $outputChannelName;
        }

        $results = [];
        foreach ($multiOutputChannelService->getMultiSearchResult() as $subOutputChannelIdentifier => $result) {

            if (!array_key_exists($subOutputChannelIdentifier, $outputChannelEnvironment['blocks'])) {
                throw new OutputChannelException(
                    $outputChannelName,
                    sprintf('Sub output channel with identifier "%s" not found after calling getMultiSearchResult.', $subOutputChannelIdentifier)
                );
            }

            $filter = $filters[$subOutputChannelIdentifier];
            $query = $queries[$subOutputChannelIdentifier];
            $ocOptions = $outputChannelOptions[$subOutputChannelIdentifier];
            $outputChannelName = $subOutputChanelNames[$subOutputChannelIdentifier];

            /** @var FilterStackWorker $filterStackWorker */
            $filterStackWorker = $filter[0];
            /** @var array $filterStackWorker */
            $filterServiceStack = $filter[1];

            $filterBlocks = $filterStackWorker->buildStackViewVars($filterServiceStack, $result, $query);

            $results[$subOutputChannelIdentifier] = $this->parseResult(
                $contextDefinition,
                $runtimeOptionsProvider,
                $outputChannelName,
                $ocOptions,
                $filterBlocks,
                $result
            );

        }

        return new MultiOutputChannelResult($results, $runtimeOptionsProvider);
    }

    /**
     * @param ContextDataInterface            $contextData
     * @param RuntimeOptionsProviderInterface $runtimeOptionsProvider
     * @param string                          $outputChannelName
     * @param array                           $outputChannelOptions
     * @param array                           $filterBlocks
     * @param mixed                           $result
     *
     * @return OutputChannelArrayResult|OutputChannelPaginatorResult
     *
     * @throws OutputChannelException
     */
    protected function parseResult(
        ContextDataInterface $contextData,
        RuntimeOptionsProviderInterface $runtimeOptionsProvider,
        string $outputChannelName,
        array $outputChannelOptions,
        array $filterBlocks,
        $result
    ) {

        $documentNormalizer = $this->getDocumentNormalizer($contextData, $outputChannelName);

        if ($outputChannelOptions['paginator']['enabled'] === true) {
            $paginator = $this->paginatorFactory->create(
                $result,
                $outputChannelOptions['paginator']['adapter_class'],
                $outputChannelName,
                $contextData,
                $documentNormalizer
            );

            $paginator->setItemCountPerPage($runtimeOptionsProvider->getMaxPerPage());
            $paginator->setCurrentPageNumber($runtimeOptionsProvider->getCurrentPage());

            $paginatorOutputResult = new OutputChannelPaginatorResult(
                $contextData->getName(),
                $outputChannelName,
                $filterBlocks,
                $runtimeOptionsProvider
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
            $runtimeOptionsProvider
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
        $outputChannelName = $outputChannelContext->getOutputChannelName();
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

        $outputChannelContext->getRuntimeOptionsProvider()->setDefaultOptions($outputChannelOptions);

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
     * @return RuntimeOptionsProviderInterface
     *
     * @throws OutputChannelException
     */
    protected function getOCRuntimeOptionsProvider(ContextDataInterface $contextDefinition, string $outputChannelName)
    {
        $outputChannelRuntimeOptionsProviderName = $contextDefinition->getOutputChannelRuntimeOptionsProvider($outputChannelName);
        $runtimeOptionsProvider = $this->outputChannelManager->getOutputChannelRuntimeOptionsProvider($outputChannelRuntimeOptionsProviderName);
        if (!$runtimeOptionsProvider instanceof RuntimeOptionsProviderInterface) {
            throw new OutputChannelException($outputChannelName,
                sprintf('could not load runtime options provider "%s" for context "%s"', $outputChannelRuntimeOptionsProviderName, $contextDefinition->getName()));
        }

        return $runtimeOptionsProvider;
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
