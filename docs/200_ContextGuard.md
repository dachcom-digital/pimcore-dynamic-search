# Context Guard

The context guard adds an additional layer of protection and controll on top of the set of your indexing rules. It allows you to filter the result and avoid indexing unwanted content.

To  implement a guard add a service that would implement `ContextGuardInterface`.

## Example Setup

``` yaml
services:
  App\DynamicSearch\Guard\DefaultContextGuard:
    tags:
      - { name: dynamic_search.context_guard }
```
This simple guard will prevent the document with the id 1 (it is the root document in the pimcore document tree) to be added to the index. It does not check the object type, so it will filter assets and data objects as well.

``` php
<?php

namespace App\DynamicSearch\Guard;

use DynamicSearchBundle\Guard\ContextGuardInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;

class DefaultContextGuard implements ContextGuardInterface
{
    public function verifyResourceMetaForContext(string $contextName, ResourceMetaInterface $resourceMeta): bool
    {
        if ($resourceMeta->getResourceId() > 1) {
            return true;
        }

        return false;
    }
}

```