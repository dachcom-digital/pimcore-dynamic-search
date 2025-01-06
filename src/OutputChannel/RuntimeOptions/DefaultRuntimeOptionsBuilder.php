<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

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

        $obj->offsetSet('prefix', $prefix);
        $obj->offsetSet('current_page', $this->getCurrentPage($prefix));
        $obj->offsetSet('page_identifier', $this->getPageIdentifier($prefix));
        $obj->offsetSet('request_query_vars', $this->getRequestQueryVars($prefix));

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
