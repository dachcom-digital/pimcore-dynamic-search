<?php

namespace DynamicSearchBundle\OutputChannel\RuntimeOptions;

interface RuntimeQueryProviderInterface
{
    public function getUserQuery(): ?string;

    public function getUserLocale(): ?string;

    public function getQueryIdentifier(): string;
}
