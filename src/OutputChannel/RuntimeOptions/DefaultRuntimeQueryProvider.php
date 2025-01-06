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
