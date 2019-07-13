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
     * {@inheritdoc}
     */
    public function getUserQuery()
    {
        return $this->requestStack->getCurrentRequest()->query->get($this->getQueryIdentifier(), null);
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryIdentifier()
    {
        return 'q';
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentPage()
    {
        return $this->requestStack->getCurrentRequest()->query->get('page', 1);
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxPerPage()
    {
        return isset($this->defaultOptions['max_per_page']) && is_numeric($this->defaultOptions['max_per_page']) ? (int) $this->defaultOptions['max_per_page'] : 10;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalParameter()
    {
        return [];
    }
}
