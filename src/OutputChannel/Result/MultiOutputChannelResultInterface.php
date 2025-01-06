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

use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

interface MultiOutputChannelResultInterface
{
    /**
     * @return array<int, OutputChannelResultInterface>
     */
    public function getResults(): array;

    /**
     * @return RuntimeQueryProviderInterface
     */
    public function getRuntimeQueryProvider(): RuntimeQueryProviderInterface;
}
