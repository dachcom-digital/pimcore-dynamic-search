<?php

namespace DynamicSearchBundle\OutputChannel\RuntimeOptions;

use Symfony\Component\HttpFoundation\RequestStack;

class DefaultRuntimeOptionsProvider implements RuntimeOptionsProviderInterface
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var array
     */
    protected $defaultOptions;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

     public function setDefaultOptions(array $options = [])
     {
         $this->defaultOptions = $options;
     }

    /**
     * {@inheritDoc}
     */
    public function getUserQuery()
    {
        return $this->requestStack->getCurrentRequest()->query->get($this->getQueryIdentifier(), null);
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryIdentifier()
    {
        return 'q';
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentPage()
    {
        return $this->requestStack->getCurrentRequest()->query->get('page', 1);
    }

    /**
     * {@inheritDoc}
     */
    public function getMaxPerPage()
    {
        return isset($this->defaultOptions['max_per_page']) && is_numeric($this->defaultOptions['max_per_page']) ? (int) $this->defaultOptions['max_per_page'] : 10;
    }

    /**
     * {@inheritDoc}
     */
    public function getAdditionalParameter()
    {
        return [];
    }
}