<?php

namespace DynamicSearchBundle\State;

interface HealthStateInterface
{
    public const STATE_OK = 0;
    public const STATE_WARNING = 1;
    public const STATE_ERROR = 2;
    public const STATE_SILENT = 3;

    public function getModuleName(): string;

    public function getState(): int;

    public function getTitle(): string;

    public function getComment(): string;
}
