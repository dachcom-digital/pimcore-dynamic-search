<?php

namespace DynamicSearchBundle\Normalizer\Resource;

interface ResourceMetaInterface
{
    /**
     * @return string|int
     */
    public function getDocumentId();

    /**
     * @return string
     */
    public function getResourceCollectionType();

    /**
     * @return string
     */
    public function getResourceType();

    /**
     * @return string|null
     */
    public function getResourceSubType();

    /**
     * @return mixed
     */
    public function getResourceId();

    /**
     * @return array
     */
    public function getResourceOptions();

    /**
     * @param string $option
     *
     * @return bool
     */
    public function hasNormalizerOption(string $option);

    /**
     * @param string $option
     *
     * @return mixed
     */
    public function getNormalizerOption(string $option);

    /**
     * @return array
     */
    public function getNormalizerOptions();
}
