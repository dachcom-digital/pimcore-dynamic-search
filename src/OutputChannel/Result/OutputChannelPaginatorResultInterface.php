<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use Knp\Component\Pager\Pagination\PaginationInterface;

interface OutputChannelPaginatorResultInterface
{
    public function getPaginator(): PaginationInterface;
}
