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
    public function __construct(
        protected OutputChannelContextInterface $context,
        protected FilterDefinitionManagerInterface $filterDefinitionManager,
        protected IndexManagerInterface $indexManager
    ) {
    }

    public function enrichStackQuery(array $filterStack, mixed $query): mixed
    {
        foreach ($filterStack as $filterService) {
            $query = $this->prepareFilter($filterService)->enrichQuery($query);
        }

        return $query;
    }

    public function buildStackViewVars(SearchContainerInterface $searchContainer, array $filterStack): array
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

    protected function prepareFilter(array $filterData): FilterInterface
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
     * @throws OutputChannelException
     */
    public function generateFilterServiceStack(OutputChannelContextInterface $outputChannelContext, OutputChannelModifierEventDispatcher $eventDispatcher): array
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
