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

namespace DynamicSearchBundle\Service;

interface LockServiceInterface
{
    public const CONTEXT_INDEXING = 'context_indexing';
    public const QUEUE_INDEXING = 'queue_indexing';

    public function isLocked(string $token): bool;

    public function getLockMessage(string $token): string;

    public function lock(string $token, string $executor, int $lifeTime = 14400): void;

    public function unlock(string $token): void;
}
