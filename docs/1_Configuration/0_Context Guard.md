# Context Guard

The context guard add an additional layer of protection and controll ontop of the set of your indexing rules. It allows you to filter the result and avoid indexing unvanted content.

To  implement a guard add a service that would implement `ContextGuardInterface`.

## Example Setup

``` yaml
services:
  AppBundle\DynamicSearch\Guard\DefaultContextGuard:
    tags:
      - { name: dynamic_search.context_guard }
```
This simple guard will prevent the document with the id 1 (it is the root document in the pimcore document tree) to be added to the index.

``` php
<?php

namespace AppBundle\DynamicSearch\Guard;

use DynamicSearchBundle\Guard\ContextGuardInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

class DefaultContextGuard implements ContextGuardInterface
{
    /**
     * {@inheritDoc}
     */
    public function verifyResourceMetaForContext(string $contextName, ResourceMetaInterface $resourceMeta)
    {
        if ($resourceMeta->getResourceId() > 1) {
            return true;
        }

        return false;
    }
}

```