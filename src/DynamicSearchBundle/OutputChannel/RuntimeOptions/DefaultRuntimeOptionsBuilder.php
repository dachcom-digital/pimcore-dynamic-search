<?php

namespace DynamicSearchBundle\OutputChannel\RuntimeOptions;

use Symfony\Component\HttpFoundation\RequestStack;

class DefaultRuntimeOptionsBuilder implements RuntimeOptionsBuilderInterface
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
    public function buildOptions(?string $prefix)
    {
        $obj = new \ArrayObject();

        $obj['prefix'] = $prefix;
        $obj['current_page'] = $this->getCurrentPage($prefix);
        $obj['page_identifier'] = $this->getPageIdentifier($prefix);
        $obj['request_query_vars'] = $this->getRequestQueryVars($prefix);

        return $obj;
    }

    /**
     * @param string $prefix
     *
     * @return array
     */
    protected function getRequestQueryVars(?string $prefix)
    {
        $queryData = $this->requestStack->getCurrentRequest()->query->all();

        if ($prefix !== null && isset($queryData[$prefix])) {
            return $queryData[$prefix];
        }

        return $queryData;
    }

    /**
     * @param string $prefix
     *
     * @return string
     */
    protected function getPageIdentifier(?string $prefix)
    {
        return is_null($prefix) ? 'page' : sprintf('%s_page', $prefix);
    }

    /**
     * @param string $prefix
     *
     * @return mixed
     */
    protected function getCurrentPage(?string $prefix)
    {
        return $this->requestStack->getCurrentRequest()->query->get($this->getPageIdentifier($prefix), 1);
    }

    /**
     * @return array
     */
    protected function getAdditionalParameter()
    {
        return [];
    }
}
