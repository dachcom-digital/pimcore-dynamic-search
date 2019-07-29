<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsProviderInterface;
use DynamicSearchBundle\Paginator\PaginatorInterface;

class OutputChannelPaginatorResult implements OutputChannelPaginatorResultInterface
{
    /**
     * @var string
     */
    protected $contextName;

    /**
     * @var string
     */
    protected $outputChannelName;

    /**
     * @var array
     */
    protected $filter;

    /**
     * @var RuntimeOptionsProviderInterface
     */
    protected $runtimeOptionsProvider;

    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * @param string                          $contextName
     * @param string                          $outputChannelName
     * @param array                           $filter
     * @param RuntimeOptionsProviderInterface $runtimeOptionsProvider
     * @param PaginatorInterface              $paginator
     */
    public function __construct(
        string $contextName,
        string $outputChannelName,
        array $filter,
        RuntimeOptionsProviderInterface $runtimeOptionsProvider,
        PaginatorInterface $paginator
    ) {
        $this->contextName = $contextName;
        $this->outputChannelName = $outputChannelName;
        $this->filter = $filter;
        $this->runtimeOptionsProvider = $runtimeOptionsProvider;
        $this->paginator = $paginator;
    }

    /**
     * {@inheritdoc}
     */
    public function getContextName()
    {
        return $this->contextName;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelName()
    {
        return $this->outputChannelName;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        return $this->paginator;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginator()
    {
        return $this->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getRuntimeOptionsProvider()
    {
        return $this->runtimeOptionsProvider;
    }
}
