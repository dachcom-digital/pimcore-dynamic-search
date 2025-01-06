<?php

namespace DynamicSearchBundle\OutputChannel\Query;

class MultiSearchContainer implements MultiSearchContainerInterface
{
    public function __construct(protected array $searchContainer)
    {
    }

    public function getSearchContainer(): array
    {
        return $this->searchContainer;
    }
}
