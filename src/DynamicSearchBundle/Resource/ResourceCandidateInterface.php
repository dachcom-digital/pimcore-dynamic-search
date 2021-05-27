<?php

namespace DynamicSearchBundle\Resource;

interface ResourceCandidateInterface
{
    /**
     * @return bool
     */
    public function isAllowedToModifyDispatchType();

    /**
     * @return bool
     */
    public function isAllowedToModifyResource();

    /**
     * @param mixed|null $resource
     *
     * @throws \Exception
     */
    public function setResource($resource);

    /**
     * @return mixed|null
     */
    public function getResource();

    /**
     * @param string $dispatchType
     *
     * @throws \Exception
     */
    public function setDispatchType(string $dispatchType);

    /**
     * @return string|null
     */
    public function getDispatchType();
}
