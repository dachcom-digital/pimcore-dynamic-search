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
use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierActionInterface;
use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierFilterInterface;
use DynamicSearchBundle\OutputChannel\OutputChannelInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsBuilderInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

interface OutputChannelManagerInterface
{
    /**
     * @throws ProviderException
     */
    public function getOutputChannel(ContextDefinitionInterface $contextDefinition, string $outputChannelName): ?OutputChannelInterface;

    public function getOutputChannelRuntimeQueryProvider(string $provider): ?RuntimeQueryProviderInterface;

    public function getOutputChannelRuntimeOptionsBuilder(string $provider): ?RuntimeOptionsBuilderInterface;

    /**
     * @return array<int, OutputChannelModifierActionInterface>
     */
    public function getOutputChannelModifierAction(string $outputChannelServiceName, string $action): array;

    public function getOutputChannelModifierFilter(string $outputChannelServiceName, string $filter): ?OutputChannelModifierFilterInterface;
}
