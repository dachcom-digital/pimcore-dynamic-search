<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\Paginator\PaginatorInterface;

class OutputChannelPaginatorResult extends OutputChannelResult implements OutputChannelPaginatorResultInterface
{
    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * {@inheritdoc}
     */
    public function setPaginator(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

}
