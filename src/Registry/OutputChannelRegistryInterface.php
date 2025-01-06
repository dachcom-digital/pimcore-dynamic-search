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

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierActionInterface;
use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierFilterInterface;
use DynamicSearchBundle\OutputChannel\OutputChannelInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsBuilderInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

interface OutputChannelRegistryInterface
{
    public function hasOutputChannelService(string $identifier): bool;

    public function getOutputChannelService(string $identifier): ?OutputChannelInterface;

    public function hasOutputChannelRuntimeQueryProvider(string $identifier): bool;

    public function getOutputChannelRuntimeQueryProvider(string $identifier): ?RuntimeQueryProviderInterface;

    public function hasOutputChannelRuntimeOptionsBuilder(string $identifier): bool;

    public function getOutputChannelRuntimeOptionsBuilder(string $identifier): ?RuntimeOptionsBuilderInterface;

    public function hasOutputChannelModifierAction(string $outputChannelServiceName, string $action): bool;

    /**
     * @return array<int, OutputChannelModifierActionInterface>
     */
    public function getOutputChannelModifierAction(string $outputChannelServiceName, string $action): array;

    public function hasOutputChannelModifierFilter(string $outputChannelServiceName, string $filter): bool;

    public function getOutputChannelModifierFilter(string $outputChannelServiceName, string $filter): ?OutputChannelModifierFilterInterface;
}
