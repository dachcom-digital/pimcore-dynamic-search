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

namespace DynamicSearchBundle\State;

use DynamicSearchBundle\Provider\IndexProviderInterface;
use DynamicSearchBundle\Registry\IndexProviderRegistryInterface;

class IndexProviderHealthState implements HealthStateInterface
{
    public function __construct(protected IndexProviderRegistryInterface $indexProviderRegistry)
    {
    }

    public function getModuleName(): string
    {
        return 'Dynamic Search';
    }

    public function getState(): int
    {
        $indexProviders = $this->indexProviderRegistry->all();

        if (count($indexProviders) === 0) {
            return self::STATE_ERROR;
        }

        return self::STATE_OK;
    }

    public function getTitle(): string
    {
        return 'Available Index Provider';
    }

    public function getComment(): string
    {
        $indexProviders = $this->indexProviderRegistry->all();

        if (count($indexProviders) === 0) {
            return 'No index provider available';
        }

        return implode(', ', array_map(static function (IndexProviderInterface $provider) {
            return (new \ReflectionClass($provider))->getShortName();
        }, $indexProviders));
    }
}
