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

namespace DynamicSearchBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ErrorEvent extends Event
{
    public function __construct(
        protected string $contextName,
        protected string $message,
        protected ?string $providerName = null,
        protected ?\Exception $exception = null
    ) {
    }

    public function getContextName(): string
    {
        return $this->contextName;
    }

    public function getProviderName(): ?string
    {
        return $this->providerName;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getException(): ?\Exception
    {
        return $this->exception;
    }
}
