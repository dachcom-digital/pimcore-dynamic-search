<?php

namespace DynamicSearchBundle\OutputChannel\Query;

class MultiSearchContainer implements MultiSearchContainerInterface
{
    protected array $searchContainer;

    public function __construct(array $searchContainer)
    {
        $this->searchContainer = $searchContainer;
    }

    public function getSearchContainer(): array
    {
        return $this->searchContainer;
    }
}
