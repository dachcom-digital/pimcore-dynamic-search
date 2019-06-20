<?php

namespace DynamicSearchBundle\Factory;

use DynamicSearchBundle\Paginator\AdapterInterface;
use DynamicSearchBundle\Paginator\PaginatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class PaginatorFactory implements PaginatorFactoryInterface
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;
    /**
     * @var string
     */
    protected $paginatorClass;

    /**
     * @param SerializerInterface $serializer
     * @param string              $paginatorClass
     */
    public function __construct(SerializerInterface $serializer, string $paginatorClass)
    {
        $this->serializer = $serializer;
        $this->paginatorClass = $paginatorClass;
    }

    /**
     * {@inheritDoc}
     */
    public function create(string $adapterClass, $adapterData)
    {
        $paginatorClassName = $this->paginatorClass;

        /** @var AdapterInterface $adapter */
        $adapter = new $adapterClass($adapterData);
        $adapter->setSerializer($this->serializer);

        /** @var PaginatorInterface $paginator */
        $paginator = new $paginatorClassName($adapter);

        return $paginator;

    }
}