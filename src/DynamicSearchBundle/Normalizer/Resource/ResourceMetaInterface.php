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
}
