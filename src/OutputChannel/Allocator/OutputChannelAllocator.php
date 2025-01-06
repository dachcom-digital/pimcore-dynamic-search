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

namespace DynamicSearchBundle\OutputChannel\Allocator;

class OutputChannelAllocator implements OutputChannelAllocatorInterface
{
    public function __construct(
        protected string $outputChannelName,
        protected ?string $parentOutputChannelName,
        protected ?string $subOutputChannelIdentifier
    ) {
    }

    public function getOutputChannelName(): string
    {
        return $this->outputChannelName;
    }

    public function getParentOutputChannelName(): ?string
    {
        return $this->parentOutputChannelName;
    }

    public function getSubOutputChannelIdentifier(): ?string
    {
        return $this->subOutputChannelIdentifier;
    }
}
