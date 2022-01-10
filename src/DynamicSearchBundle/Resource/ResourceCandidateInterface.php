<?php

namespace DynamicSearchBundle\Resource;

interface ResourceCandidateInterface
{
    public function isAllowedToModifyDispatchType(): bool;

    public function isAllowedToModifyResource(): bool;

    /**
     * @throws \Exception
     */
    public function setResource($resource): void;

    public function getResource(): mixed;

    /**
     * @throws \Exception
     */
    public function setDispatchType(string $dispatchType): void;

    public function getDispatchType(): string;
}
