# Resource Validation
In some cases, you need to validate your resource, before it should get dispatched in a specific operation like `insert`, `update` or `delete`.
Just use the `DynamicSearchEvents::RESOURCE_CANDIDATE_VALIDATION` event to manipulate the resource candidate:

```yaml
App\DynamicSearch\EventListener\ResourceCandidateListener:
    tags:
        - { name: kernel.event_subscriber }
```

```php
<?php

namespace App\DynamicSearch\EventListener;

use DynamicSearchBundle\DynamicSearchEvents;
use DynamicSearchBundle\Event\ResourceCandidateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ResourceCandidateListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            DynamicSearchEvents::RESOURCE_CANDIDATE_VALIDATION => ['validateResourceCandidate', 0]
        ];
    }

    public function validateResourceCandidate(ResourceCandidateEvent $e): void
    {
        $resourceCandidate = $e->getResourceCandidate();
        $resource = $resourceCandidate->getResource();

        // always allow dispatching delete operation as they are
        if ($resourceCandidate->getDispatchType() === 'delete') {
            return;
        }
        
        // example I: change dispatch type to force delete
        if ($yourConditionMet && $resourceCandidate->getDispatchType() === 'update' && $resourceCandidate->isAllowedToModifyDispatchType() === true) {
            $resourceCandidate->setDispatchType('delete');
        }
        
        // example II: set resource to null if it should not get indexed at all
        if ($yourConditionMet && $resourceCandidate->isAllowedToModifyResource() === true) {
            $resourceCandidate->setResource(null);
        }
    }
}
```