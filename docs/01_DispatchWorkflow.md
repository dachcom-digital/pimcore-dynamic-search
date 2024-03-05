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

#### Queue Worker
The queue is based on [symfony messenger](https://symfony.com/doc/current/messenger.html).
Resources which are dispatched to the index are put into the queue and will be processed by the worker asynchronously.
The queue is processed in batches.
If available and resource validation passes, the transformed resource will be submitted to the index provider. 

To start the queue worker, execute

```bash
$ bin/console messenger:consume dynamic_search_queue
```

> It is highly recommended to set up the worker to be always running with supervisor or systemd.  
> More details about how to set up the queue worker are found [here](https://symfony.com/doc/current/messenger.html#consuming-messages-running-the-worker).

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
