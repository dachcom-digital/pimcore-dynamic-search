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

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Exception\OutputChannelException;
use DynamicSearchBundle\OutputChannel\Result\MultiOutputChannelResultInterface;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelResultInterface;

interface OutputChannelProcessorInterface
{
    /**
     * @throws OutputChannelException
     */
    public function dispatchOutputChannelQuery(string $contextName, string $outputChannelName): OutputChannelResultInterface|MultiOutputChannelResultInterface;
}
