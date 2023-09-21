<?php

namespace DynamicSearchBundle\OutputChannel\RuntimeOptions;

use Symfony\Component\HttpFoundation\RequestStack;

class DefaultRuntimeOptionsBuilder implements RuntimeOptionsBuilderInterface
{
    public function __construct(protected RequestStack $requestStack)
    {
    }

    public function buildOptions(?string $prefix): \ArrayObject
    {
        $obj = new \ArrayObject();

        $obj['prefix'] = $prefix;
        $obj['current_page'] = $this->getCurrentPage($prefix);
        $obj['page_identifier'] = $this->getPageIdentifier($prefix);
        $obj['request_query_vars'] = $this->getRequestQueryVars($prefix);

        return $obj;
    }

    protected function getRequestQueryVars(?string $prefix): array
    {
        $queryData = $this->requestStack->getCurrentRequest()->query->all();

        if ($prefix !== null && isset($queryData[$prefix])) {
            return $queryData[$prefix];
        }

        return $queryData;
    }

    protected function getPageIdentifier(?string $prefix): string
    {
        return is_null($prefix) ? 'page' : sprintf('%s_page', $prefix);
    }

    protected function getCurrentPage(?string $prefix): int
    {
        return (int) $this->requestStack->getCurrentRequest()->query->get($this->getPageIdentifier($prefix), 1);
    }

    protected function getAdditionalParameter(): array
    {
        return [];
    }
}
