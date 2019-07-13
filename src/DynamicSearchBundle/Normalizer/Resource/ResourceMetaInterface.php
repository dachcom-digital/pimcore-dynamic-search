<?php

namespace DynamicSearchBundle\Normalizer\Resource;

interface ResourceMetaInterface
{
    /**
     * @return mixed
     */
    public function getDocumentId();

    /**
     * @return mixed
     */
    public function getResourceCollectionType();

    /**
     * @return mixed
     */
    public function getResourceType();

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
