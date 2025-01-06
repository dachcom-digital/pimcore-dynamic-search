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

namespace DynamicSearchBundle\Filter;

use DynamicSearchBundle\EventDispatcher\OutputChannelModifierEventDispatcher;
use DynamicSearchBundle\OutputChannel\Context\OutputChannelContextInterface;
use DynamicSearchBundle\OutputChannel\Query\Result\RawResultInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface FilterInterface
{
    public function configureOptions(OptionsResolver $resolver): void;

    public function setOptions(array $options): void;

    public function setName(string $name): void;

    public function setEventDispatcher(OutputChannelModifierEventDispatcher $eventDispatcher): void;

    public function setOutputChannelContext(OutputChannelContextInterface $outputChannelContext): void;

    public function supportsFrontendView(): bool;

    public function enrichQuery(mixed $query): mixed;

    public function findFilterValueInResult(RawResultInterface $rawResult): mixed;

    public function buildViewVars(RawResultInterface $rawResult, mixed $filterValues, mixed $query): ?array;
}
