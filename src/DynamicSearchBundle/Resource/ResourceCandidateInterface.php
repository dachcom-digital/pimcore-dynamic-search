<?php

namespace DynamicSearchBundle\Resource;

interface ResourceCandidateInterface
{
    public function isAllowedToModifyDispatchType(): bool;

    public function isAllowedToModifyResource(): bool;

    public function setResource($resource): void;

    public function getResource();

    public function setDispatchType(string $dispatchType): void;

    public function getDispatchType(): string;
}
