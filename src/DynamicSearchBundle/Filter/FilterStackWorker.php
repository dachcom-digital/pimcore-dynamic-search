<?php

namespace DynamicSearchBundle\Filter;

use DynamicSearchBundle\EventDispatcher\OutputChannelModifierEventDispatcher;
use DynamicSearchBundle\Exception\OutputChannelException;
use DynamicSearchBundle\Filter\Definition\FilterDefinitionInterface;
use DynamicSearchBundle\Manager\FilterDefinitionManagerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\OutputChannel\Context\OutputChannelContextInterface;
use DynamicSearchBundle\OutputChannel\Query\SearchContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterStackWorker
{
    /**
     * @var OutputChannelContextInterface
     */
    protected $context;

    /**
     * @var FilterDefinitionManagerInterface
     */
    protected $filterDefinitionManager;

    /**
     * @var IndexManagerInterface
     */
    protected $indexManager;

    /**
     * @param OutputChannelContextInterface    $context
     * @param FilterDefinitionManagerInterface $filterDefinitionManager
     * @param IndexManagerInterface            $indexManager
     */
    public function __construct(
        OutputChannelContextInterface $context,
        FilterDefinitionManagerInterface $filterDefinitionManager,
        IndexManagerInterface $indexManager
    ) {
        $this->context = $context;
        $this->filterDefinitionManager = $filterDefinitionManager;
        $this->indexManager = $indexManager;
    }

    /**
     * @param array $filterStack
     * @param mixed $query
     *
     * @return mixed
     */
    public function enrichStackQuery(array $filterStack, $query)
    {
        foreach ($filterStack as $filterService) {
            $query = $this->prepareFilter($filterService)->enrichQuery($query);
        }

        return $query;
    }

    /**
     * @param SearchContainerInterface $searchContainer
     * @param array                    $filterStack
     *
     * @return array
     */
    public function buildStackViewVars(SearchContainerInterface $searchContainer, array $filterStack)
    {
        $filterBlocks = [];
        foreach ($filterStack as $filterService) {
            $preparedFilterService = $this->prepareFilter($filterService);
            if ($preparedFilterService->supportsFrontendView() === true) {
                $filterValues = $preparedFilterService->findFilterValueInResult($searchContainer->getRawResult());
                $viewVars = $preparedFilterService->buildViewVars($searchContainer->getRawResult(), $filterValues, $searchContainer->getQuery());
                if ($viewVars !== null) {
                    $filterBlocks[] = $viewVars;
                }
            }
        }

        return $filterBlocks;
    }

    /**
     * @param array $filterData
     *
     * @return FilterInterface
     */
    protected function prepareFilter(array $filterData)
    {
        $options = new OptionsResolver();

        /** @var FilterInterface $filter */
        $filter = $filterData['filter'];

        $filter->configureOptions($options);
        $filter->setName($filterData['name']);
        $filter->setOptions($options->resolve($filterData['config']));
        $filter->setOutputChannelContext($this->context);

        return $filter;
    }

    /**
     * @param OutputChannelContextInterface        $outputChannelContext
     * @param OutputChannelModifierEventDispatcher $eventDispatcher
     *
     * @return array
     *
     * @throws OutputChannelException
     */
    public function generateFilterServiceStack(OutputChannelContextInterface $outputChannelContext, OutputChannelModifierEventDispatcher $eventDispatcher)
    {
        $outputChannelAllocator = $this->context->getOutputChannelAllocator();

        try {
            $filterDefinition = $this->filterDefinitionManager->generateFilterDefinition($this->context->getContextDefinition(), $outputChannelAllocator);
        } catch (\Throwable $e) {
            throw new OutputChannelException(
                $outputChannelAllocator->getOutputChannelName(),
                sprintf('Unable to resolve filter definition. Error was: %s', $e->getMessage())
            );
        }

        if (!$filterDefinition instanceof FilterDefinitionInterface) {
            return [];
        }

        $filterStack = [];

        foreach ($filterDefinition->getFilterDefinitions() as $filterDefinition) {
            $filterType = $filterDefinition['type'];
            $filterName = $filterDefinition['name'];
            $filterTypeConfiguration = $filterDefinition['configuration'];

            try {
                $filter = $this->indexManager->getFilter($this->context->getContextDefinition(), $filterType);
            } catch (\Throwable $e) {
                throw new OutputChannelException(
                    $outputChannelAllocator->getOutputChannelName(),
                    sprintf(
                        'Unable to fetch filter "%s". Error was: %s',
                        $filterType,
                        $e->getMessage()
                    )
                );
            }

            if (!$filter instanceof FilterInterface) {
                continue;
            }

            // filter is used by reference
            // add config and name in each usage

            $filter->setEventDispatcher($eventDispatcher);

            $filterStack[] = ['filter' => $filter, 'name' => $filterName, 'config' => $filterTypeConfiguration];
        }

        return $filterStack;
    }
}
