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

use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Registry\DataProviderRegistryInterface;

class DataProviderHealthState implements HealthStateInterface
{
    public function __construct(protected DataProviderRegistryInterface $dataProviderRegistry)
    {
    }

    public function getModuleName(): string
    {
        return 'Dynamic Search';
    }

    public function getState(): int
    {
        $dataProviders = $this->dataProviderRegistry->all();

        if (count($dataProviders) === 0) {
            return self::STATE_ERROR;
        }

        return self::STATE_OK;
    }

    public function getTitle(): string
    {
        return 'Available Data Provider';
    }

    public function getComment(): string
    {
        $dataProviders = $this->dataProviderRegistry->all();

        if (count($dataProviders) === 0) {
            return 'No data provider available';
        }

        return implode(', ', array_map(static function (DataProviderInterface $provider) {
            return (new \ReflectionClass($provider))->getShortName();
        }, $dataProviders));
    }
}
