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

namespace DynamicSearchBundle\OutputChannel\Context;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

interface OutputChannelContextInterface
{
    public function getContextDefinition(): ContextDefinitionInterface;

    public function getRuntimeQueryProvider(): RuntimeQueryProviderInterface;

    public function getRuntimeOptions(): \ArrayObject;

    public function getIndexProviderOptions(): array;

    public function getOutputChannelAllocator(): OutputChannelAllocatorInterface;

    public function getOutputChannelServiceName(): string;
}
