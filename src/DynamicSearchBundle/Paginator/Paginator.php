<?php

namespace DynamicSearchBundle\Paginator;

class Paginator implements PaginatorInterface
{
    /**
     * Returns a foreach-compatible iterator.
     * We need to overwrite this method to catch more meaningful exceptions.
     *
     * @throws \RuntimeException
     */
    public function getIterator(): \Traversable
    {
        try {
            return $this->getCurrentItems();
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Error producing an paginated iterator: %s', $e->getMessage()));
        }
    }
}
