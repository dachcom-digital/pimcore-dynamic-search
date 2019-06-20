<?php

namespace DynamicSearchBundle\Paginator;

use Symfony\Component\Serializer\SerializerInterface;

interface AdapterInterface extends \Zend\Paginator\Adapter\AdapterInterface
{
    /**
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer);
}
