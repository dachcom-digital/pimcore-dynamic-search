<?php

namespace DynamicSearchBundle\Processor\SubProcessor;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Document\Definition\OutputDocumentDefinition;
use DynamicSearchBundle\Document\Definition\OutputDocumentDefinitionInterface;
use DynamicSearchBundle\EventDispatcher\OutputChannelModifierEventDispatcher;
use DynamicSearchBundle\Exception\OutputChannelException;
use DynamicSearchBundle\Factory\PaginatorFactoryInterface;
use DynamicSearchBundle\Manager\DocumentDefinitionManagerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\Manager\OutputChannelManagerInterface;
use DynamicSearchBundle\OutputChannel\OutputChannelInterface;
use DynamicSearchBundle\OutputChannel\Result\Document\Document;
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
     * @var DocumentDefinitionManagerInterface
     */
    protected $documentDefinitionManager;

    /**
     * @var PaginatorFactoryInterface
     */
    protected $paginatorFactory;

    /**
     * @param ConfigurationInterface             $configuration
     * @param OutputChannelManagerInterface      $outputChannelManager
     * @param IndexManagerInterface              $indexManager
     * @param DocumentDefinitionManagerInterface $documentDefinitionManager
     * @param PaginatorFactoryInterface          $paginatorFactory
     */
    public function __construct(
        ConfigurationInterface $configuration,
        OutputChannelManagerInterface $outputChannelManager,
        IndexManagerInterface $indexManager,
        DocumentDefinitionManagerInterface $documentDefinitionManager,
        PaginatorFactoryInterface $paginatorFactory
    ) {
        $this->configuration = $configuration;
        $this->outputChannelManager = $outputChannelManager;
        $this->indexManager = $indexManager;
        $this->documentDefinitionManager = $documentDefinitionManager;
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
        } catch (\Throwable $e) {
            throw new OutputChannelException($outputChannelName,
                sprintf('could not determinate output channel options for "%s" for context "%s". Error was: %s', $outputChannelName, $contextName, $e->getMessage())
            );
        }

        $outputChannelRuntimeOptionsProvider->setDefaultOptions($outputChannelOptions);

        $outputChannelService->setEventDispatcher($eventDispatcher);
        $outputChannelService->setRuntimeParameterProvider($outputChannelRuntimeOptionsProvider);

        $result = $outputChannelService->execute($indexProviderOptions, $outputChannelOptions, $options);

        $outputDocumentDefinition = $this->configureOutputDocumentDefinition($contextDefinition);

        if ($outputChannelService->needsPaginator()) {

            $paginator = $this->paginatorFactory->create($result, $outputChannelService->getPaginatorAdapterClass(), $contextName, $outputChannelName,
                $outputDocumentDefinition);
            $paginator->setItemCountPerPage($outputChannelRuntimeOptionsProvider->getMaxPerPage());
            $paginator->setCurrentPageNumber($outputChannelRuntimeOptionsProvider->getCurrentPage());

            return new OutputChannelPaginatorResult(
                $contextName,
                $outputChannelName,
                $outputChannelRuntimeOptionsProvider,
                $paginator
            );
        }

        $documents = array_map(function ($document) use ($contextName, $outputChannelName, $outputDocumentDefinition) {
            return new Document($document, $contextName, $outputChannelName, $outputDocumentDefinition);
        }, $result);

        return new OutputChannelArrayResult(
            $contextName,
            $outputChannelName,
            $outputChannelRuntimeOptionsProvider,
            $documents
        );

    }

    /**
     * @param ContextDataInterface $contextDefinition
     *
     * @return OutputDocumentDefinitionInterface
     */
    protected function configureOutputDocumentDefinition(ContextDataInterface $contextDefinition)
    {
        //@todo: cache document output definition for faster dispatch

        $definition = new OutputDocumentDefinition();
        $documentDefinitionBuilder = $this->documentDefinitionManager->getDocumentDefinitionBuilder($contextDefinition);

        if (!$documentDefinitionBuilder instanceof DocumentDefinitionBuilderInterface) {
            return $definition;
        }

        $fieldOutputDefinitions = $documentDefinitionBuilder->buildOutputDefinition($definition);

        return $fieldOutputDefinitions;
    }
}
