<?php

namespace DynamicSearchBundle\Paginator;

class Paginator extends \Laminas\Paginator\Paginator implements PaginatorInterface
{
    public function getIterator(): \Traversable
    {
        try {
            return $this->getCurrentItems();
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Error producing an paginated iterator: %s', $e->getMessage()));
        }
    }
}
