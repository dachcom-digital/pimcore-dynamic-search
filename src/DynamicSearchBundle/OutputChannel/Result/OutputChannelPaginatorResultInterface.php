<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\Paginator\PaginatorInterface;

interface OutputChannelPaginatorResultInterface
{
    /**
     * @return PaginatorInterface
     */
    public function getPaginator();
}
