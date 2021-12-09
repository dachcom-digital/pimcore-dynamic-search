# Output Channel Filters / Actions

## Filters

@todo

## Actions

For most scenarios, especially when using an index provider bundle with existing output channel implementations, it is not required to create a new output channel if you want to modify the search behaviour (fields, boosting, etc.).
Dynamic search offers the ability to modify the query by listening to actions.

### Basic steps

- create a service tagged with
  - `name`: `dynamic_search.output_channel.modifier.action`
  - `output_channel_service_identifier`: `all` to hook into all registered output channels, or the service identifier of one specific output channel.
  - `action`: the action (event) to which the modifier should listen. for valid action identifiers, check the docs of the package which is providing the output channel  
    you are using (implementation of `OutputChannelInterface`), or search for `$this->eventDispatcher->dispatchAction` in the output channel implementation  
  - `priority`: the priority, default `0`
- your modifier must implement `OutputChannelModifierActionInterface`
- add your implementation for `dispatchAction`, e.g. modify the query in `OutputModifierEvent`

### Example

The following example shows how to modify the query in the `SearchOutputChannel` of the [elastic search](https://github.com/dachcom-digital/pimcore-dynamic-search-index-provider-elasticsearch) package:

**config/services.yaml**
```yaml
    App\DynamicSearch\OutputChannel\Action\Search\MySearchAction:
        tags:
            - { name: dynamic_search.output_channel.modifier.action, output_channel_service_identifier: all, action: post_query_build }
```

**src/DynamicSearch/OutputChannel/Action/Search/MySearchAction.php**
```php
<?php

namespace App\DynamicSearch\OutputChannel\Action\Search;

use DynamicSearchBundle\Event\OutputModifierEvent;
use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;
use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierActionInterface;

class MySearchAction implements OutputChannelModifierActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function dispatchAction(string $action, OutputChannelAllocatorInterface $outputChannelAllocator, OutputModifierEvent $event): OutputModifierEvent
    {
        /** @var Search $query */
        $query = $event->getParameter('query');
        $term = $event->getParameter('term');
        $outputChannelName = $outputChannelAllocator->getOutputChannelName();

        if ($outputChannelName !== 'search') {
            return $event;
        }
        if ($action !== 'post_query_build') {
            return $event;
        }
        
        // modify query here, e.g. $query->addQuery(...) 

        $event->setParameter('query', $query);

        return $event;
    }
```
