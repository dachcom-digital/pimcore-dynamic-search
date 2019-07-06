<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsProviderInterface;
use DynamicSearchBundle\Paginator\PaginatorInterface;

class OutputChannelPaginatorResult implements OutputChannelPaginatorResultInterface
{
    protected $contextName;

    protected $outputChannelName;

    protected $runtimeOptionsProvider;

    protected $paginator;

    /**
     * @param string                          $contextName
     * @param string                          $outputChannelName
     * @param RuntimeOptionsProviderInterface $runtimeOptionsProvider
     * @param PaginatorInterface              $paginator
     */
    public function __construct(
        string $contextName,
        string $outputChannelName,
        RuntimeOptionsProviderInterface $runtimeOptionsProvider,
        PaginatorInterface $paginator
    ) {
        $this->contextName = $contextName;
        $this->outputChannelName = $outputChannelName;
        $this->runtimeOptionsProvider = $runtimeOptionsProvider;
        $this->paginator = $paginator;
    }

    /**
     * {@inheritDoc}
     */
    public function getContextName()
    {
        return $this->contextName;
    }

    /**
     * {@inheritDoc}
     */
    public function getOutputChannelName()
    {
        return $this->outputChannelName;
    }

    /**
     * {@inheritDoc}
     */
    public function getResult()
    {
        return $this->paginator;
    }

    /**
     * {@inheritDoc}
     */
    public function getPaginator()
    {
        return $this->getResult();
    }

    /**
     * {@inheritDoc}
     */
    public function getRuntimeOptionsProvider()
    {
        return $this->runtimeOptionsProvider;
    }

}