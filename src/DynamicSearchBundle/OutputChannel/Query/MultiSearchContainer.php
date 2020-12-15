<?php

namespace DynamicSearchBundle\OutputChannel\Query;

class MultiSearchContainer implements MultiSearchContainerInterface
{
    /**
     * @var array|SearchContainerInterface[]
     */
    protected $searchContainer;

    /**
     * @param array|SearchContainerInterface[]
     */
    public function __construct(array $searchContainer)
    {
        $this->searchContainer = $searchContainer;
    }

    /**
     * {@inheritDoc}
     */
    public function getSearchContainer()
    {
        return $this->searchContainer;
    }
}
