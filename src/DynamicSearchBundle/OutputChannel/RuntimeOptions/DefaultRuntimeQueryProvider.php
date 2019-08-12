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
        return $this->requestStack->getMasterRequest()->query->get($this->getQueryIdentifier(), null);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserLocale()
    {
        $locale = $this->requestStack->getMasterRequest()->query->get('locale', null);

        if ($locale === null) {
            $locale = $this->requestStack->getMasterRequest()->getLocale();
        }

        return $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryIdentifier()
    {
        return 'q';
    }
}
