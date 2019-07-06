<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\Paginator\PaginatorInterface;

interface OutputChannelPaginatorResultInterface extends OutputChannelResultInterface
{
    /**
     * @return PaginatorInterface
     */
    public function getPaginator();
}