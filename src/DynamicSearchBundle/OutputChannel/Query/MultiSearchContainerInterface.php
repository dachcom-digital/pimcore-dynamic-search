<?php

namespace DynamicSearchBundle\OutputChannel\Query;

interface MultiSearchContainerInterface
{
    /**
     * @return SearchContainerInterface[]
     */
    public function getSearchContainer(): array;
}
