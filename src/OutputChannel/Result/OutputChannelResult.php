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

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

class OutputChannelResult implements OutputChannelResultInterface
{
    public function __construct(
        protected string $contextName,
        protected int $hitCount,
        protected OutputChannelAllocatorInterface $outputChannelAllocator,
        protected array $filter,
        protected \ArrayObject $runtimeOptions,
        protected RuntimeQueryProviderInterface $runtimeQueryProvider
    ) {
    }

    public function getContextName(): string
    {
        return $this->contextName;
    }

    public function getHitCount(): int
    {
        return $this->hitCount;
    }

    public function getOutputChannelAllocator(): OutputChannelAllocatorInterface
    {
        return $this->outputChannelAllocator;
    }

    public function getFilter(): array
    {
        return $this->filter;
    }

    public function getRuntimeQueryProvider(): RuntimeQueryProviderInterface
    {
        return $this->runtimeQueryProvider;
    }

    public function getRuntimeOptions(): \ArrayObject
    {
        return $this->runtimeOptions;
    }
}
