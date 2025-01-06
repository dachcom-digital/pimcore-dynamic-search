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

namespace DynamicSearchBundle\Provider;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\ProcessCancelledException;
use DynamicSearchBundle\Exception\ProviderException;

interface ProviderInterface
{
    public function setOptions(array $options): void;

    /**
     * @throws ProviderException
     * @throws ProcessCancelledException
     */
    public function warmUp(ContextDefinitionInterface $contextDefinition): void;

    /**
     * @throws ProviderException
     * @throws ProcessCancelledException
     */
    public function coolDown(ContextDefinitionInterface $contextDefinition): void;

    public function cancelledShutdown(ContextDefinitionInterface $contextDefinition): void;

    public function emergencyShutdown(ContextDefinitionInterface $contextDefinition): void;
}
