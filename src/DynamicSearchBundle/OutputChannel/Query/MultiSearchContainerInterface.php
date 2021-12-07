<?php

namespace DynamicSearchBundle\OutputChannel\Query;

interface MultiSearchContainerInterface
{
    /**
     * @return array<int, SearchContainerInterface>
     */
    public function getSearchContainer(): array;
}
