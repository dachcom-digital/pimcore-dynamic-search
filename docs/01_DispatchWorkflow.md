## Dispatch Workflow
Initially, you need to create and fill your index by executing the run command:

> Use the `-v flag to output some log information`

```bash
$ bin/console dynamic-search:run -v
```

> There could be more to consider, depending on your data- and/or index provider.

### Listener
If you want to update your index after modifying pimcore elements, you need to enable it first
This tells DynamicSearch to register a dedicated maintenance task:

```yaml
dynamic_search:
    enable_pimcore_element_listener: true
```

#### Element Watcher
At every modification/deletion event of every pimcore element,
DynamicSearch will validate this element by calling [ResourceValidator](./40_ResourceValidator.md). 
If the resource is still present, DynamicSearch creates an `Envelope` which will be passed to a dedicated queue.

#### Element Processor
Another maintenance task processes that queue (Interval depends on your maintenance cronjob).
If available, the `Envelope` will be submitted to the index provider. 

> DynamicSearch will sort envelopes by creation date. 
> If you're updating your element multiple times before the element processor kicks in,
> only the latest envelope will be used. This allows us to save some trees. 

#### Element Process Command
There is a secret command which allows you to dispatch the queue processor immediately. 
This comes in handy if you're debugging your application:

> Use the `-v flag to output some log information`

```bash
$ bin/console dynamic-search:check-queue -v
```

#### Inheritance
Inheritance is unknown to DynamicSearch. If you're updating an object, that very object will be transmitted to the queue.
But in some cases, you're working with variants and those should get updated too.
Lets' enable it:

```yaml
dynamic_search:
    element_inheritance: 
        enabled: true
```

#### Inheritance Dispatch Origin
By default, a real user must be involved to dispatch the inheritance check. 
Which means, only if the updated comes from the pimcore backend, the child elements will be added to the queue.
We're doing this to avoid confusion.
If you're updating elements via api, you're most likely handling child elements there, so we don't need to add an extra round.  

However, you could disable it to allow queued inherited elements at every state:

```yaml
dynamic_search:
    element_inheritance: 
        origin_dispatch: 'all'
```
