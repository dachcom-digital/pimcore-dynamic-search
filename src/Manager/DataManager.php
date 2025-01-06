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

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Registry\DataProviderRegistryInterface;

class DataManager implements DataManagerInterface
{
    public function __construct(protected DataProviderRegistryInterface $dataProviderRegistry)
    {
    }

    public function getDataProvider(ContextDefinitionInterface $contextDefinition, string $providerBehaviour): DataProviderInterface
    {
        $dataProviderName = $contextDefinition->getDataProviderName();

        if (is_null($dataProviderName) || !$this->dataProviderRegistry->has($dataProviderName)) {
            throw new ProviderException('Invalid requested data provider', $dataProviderName);
        }

        $dataProvider = $this->dataProviderRegistry->get($dataProviderName);
        $dataProvider->setOptions($contextDefinition->getDataProviderOptions($providerBehaviour));

        return $dataProvider;
    }
}
