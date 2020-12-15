<?php

namespace DynamicSearchBundle\OutputChannel\Query;

interface MultiSearchContainerInterface
{
    /**
     * @return array|SearchContainerInterface[]
     */
    public function getSearchContainer();
}
