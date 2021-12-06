<?php

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
