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

class OutputChannelContext implements OutputChannelContextInterface
{
    protected ContextDefinitionInterface $contextDefinition;
    protected RuntimeQueryProviderInterface $runtimeQueryProvider;
    protected \ArrayObject $runtimeOptions;
    protected array $indexProviderOptions;
    protected OutputChannelAllocatorInterface $outputChannelAllocator;
    protected string $outputChannelServiceName;

    public function setContextDefinition(ContextDefinitionInterface $contextDefinition): void
    {
        $this->contextDefinition = $contextDefinition;
    }

    public function getContextDefinition(): ContextDefinitionInterface
    {
        return $this->contextDefinition;
    }

    public function setRuntimeQueryProvider(RuntimeQueryProviderInterface $runtimeQueryProvider): void
    {
        $this->runtimeQueryProvider = $runtimeQueryProvider;
    }

    public function getRuntimeQueryProvider(): RuntimeQueryProviderInterface
    {
        return $this->runtimeQueryProvider;
    }

    public function setRuntimeOptions(\ArrayObject $runtimeOptions): void
    {
        $this->runtimeOptions = $runtimeOptions;
    }

    public function getRuntimeOptions(): \ArrayObject
    {
        return $this->runtimeOptions;
    }

    public function setIndexProviderOptions(array $indexProviderOptions): void
    {
        $this->indexProviderOptions = $indexProviderOptions;
    }

    public function getIndexProviderOptions(): array
    {
        return $this->indexProviderOptions;
    }

    public function setOutputChannelAllocator(OutputChannelAllocatorInterface $outputChannelAllocator): void
    {
        $this->outputChannelAllocator = $outputChannelAllocator;
    }

    public function getOutputChannelAllocator(): OutputChannelAllocatorInterface
    {
        return $this->outputChannelAllocator;
    }

    public function setOutputChannelServiceName(string $outputChannelServiceName): void
    {
        $this->outputChannelServiceName = $outputChannelServiceName;
    }

    public function getOutputChannelServiceName(): string
    {
        return $this->outputChannelServiceName;
    }
}
