<?php

namespace DynamicSearchBundle\Paginator;

class Paginator extends \Zend\Paginator\Paginator implements PaginatorInterface
{
    /**
     * Returns a foreach-compatible iterator.
     * We need to overwrite this method to catch more meaningful exceptions.
     *
     * @return \Traversable
     * @throws \RuntimeException
     */
    public function getIterator()
    {
        try {
            return $this->getCurrentItems();
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Error producing an paginated iterator: %s', $e->getMessage()));
        }
    }
}
