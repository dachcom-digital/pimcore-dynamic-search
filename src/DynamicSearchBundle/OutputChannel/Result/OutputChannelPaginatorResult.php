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
