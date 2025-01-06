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

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Exception\SilentException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

interface ResourceRunnerInterface
{
    /**
     * @throws SilentException
     */
    public function runResourceStack(string $contextName, string $dispatchType, array $resourceMetaStack): void;

    /**
     * @throws SilentException
     */
    public function runResource(string $contextName, string $dispatchType, ResourceMetaInterface $resourceMeta): void;

    /**
     * @throws SilentException
     */
    public function runDelete(string $contextName, ResourceMetaInterface $resourceMeta): void;

    /**
     * @throws SilentException
     */
    public function runDeleteStack(string $contextName, array $resourceMetaStack): void;
}
