<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\OutputChannel\OutputChannelInterface;
use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierActionInterface;
use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierFilterInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsBuilderInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

interface OutputChannelRegistryInterface
{
    public function hasOutputChannelService(string $identifier): bool;

    public function getOutputChannelService(string $identifier): OutputChannelInterface;

    public function hasOutputChannelRuntimeQueryProvider(string $identifier): bool;

    public function getOutputChannelRuntimeQueryProvider(string $identifier): RuntimeQueryProviderInterface;

    public function hasOutputChannelRuntimeOptionsBuilder(string $identifier): bool;

    public function getOutputChannelRuntimeOptionsBuilder(string $identifier): RuntimeOptionsBuilderInterface;

    public function hasOutputChannelModifierAction(string $outputChannelServiceName, string $action): bool;

    /**
     * @return OutputChannelModifierActionInterface[]
     */
    public function getOutputChannelModifierAction(string $outputChannelServiceName, string $action): array;

    public function hasOutputChannelModifierFilter(string $outputChannelServiceName, string $filter): bool;

    public function getOutputChannelModifierFilter(string $outputChannelServiceName, string $filter): OutputChannelModifierFilterInterface;
}
