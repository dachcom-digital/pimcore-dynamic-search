<?php

namespace DynamicSearchBundle\OutputChannel\RuntimeOptions;

use Symfony\Component\HttpFoundation\RequestStack;

class DefaultRuntimeQueryProvider implements RuntimeQueryProviderInterface
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
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
}
