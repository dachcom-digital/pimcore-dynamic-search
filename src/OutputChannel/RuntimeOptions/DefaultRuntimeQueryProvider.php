<?php

namespace DynamicSearchBundle\OutputChannel\RuntimeOptions;

use Symfony\Component\HttpFoundation\RequestStack;

class DefaultRuntimeQueryProvider implements RuntimeQueryProviderInterface
{
    public function __construct(protected RequestStack $requestStack)
    {
    }

    public function getUserQuery(): ?string
    {
        return $this->requestStack->getMainRequest()->query->get($this->getQueryIdentifier(), null);
    }

    public function getUserLocale(): ?string
    {
        $locale = $this->requestStack->getMainRequest()->query->get('locale', null);

        if ($locale === null) {
            $locale = $this->requestStack->getMainRequest()?->getLocale();
        }

        return $locale;
    }

    public function getQueryIdentifier(): string
    {
        return 'q';
    }
}
