# Create Output Channel

## Basic steps
- create a service tagged with `name`: `dynamic_search.output_channel` and a meaningful `identifer`
- register your service:
    ```yaml
    dynamic_search:
        context:
            default:
                output_channels:
                    my_output:
                        service: <your-identifer>
    ```
- your service must implement `DynamicSearchBundle\OutputChannel\OutputChannelInterface`
  - add your implementation in `getQuery` and `getResult`
  - optional: define `options` for your output channel

## Example

**app/config/services.yaml**
```yaml
services:
    AppBundle\DynamicSearch\OutputChannel\MyOutputChannel:
        tags:
            - { name: dynamic_search.output_channel, identifier: my_output_channel }
```

**app/config/config.yaml**
  ```yaml
dynamic_search:
    context:
        default:
            output_channels:
                my_output:
                    service: 'my_output_channel'
                    use_frontend_controller: true
                    options:
                        query_option: true
                        result_option: false
```

**AppBundle/DynamicSearch/OutputChannel/MyOutputChannel.php** 
```php

namespace AppBundle\DynamicSearch\OutputChannel;

use DynamicSearchBundle\EventDispatcher\OutputChannelModifierEventDispatcher;
use DynamicSearchBundle\OutputChannel\Context\OutputChannelContextInterface;
use DynamicSearchBundle\OutputChannel\OutputChannelInterface;
use DynamicSearchBundle\OutputChannel\Query\SearchContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MyOutputChannel implements OutputChannelInterface
{
    // properties, getters and setters of OutputChannelInterface go here
    // omitted in this example ...

    /**
     * {@inheritdoc}
     */
    public static function configureOptions(OptionsResolver $optionsResolver)
    {
        // define your options
        $optionsResolver->setRequired([
            'query_option'
        ]);

        $optionsResolver->setDefaults([
            'query_option' => false,
            'result_option' => true
        ]);

        $optionsResolver->setAllowedTypes('query_option', ['bool']);
        $optionsResolver->setAllowedTypes('result_option', ['bool']);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {        
        $queryTerm = $this->outputChannelContext->getRuntimeQueryProvider()->getUserQuery();
        
        // create your query
        $query = ...
        
        // respect your options
        if ($this->options['query_option']) {
            // do something with $query
        }
        
        return $query;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getResult(SearchContainerInterface $searchContainer): SearchContainerInterface
    {
        $query = $searchContainer->getQuery();

        // get your results
        $result = ...
        
        // respect your options
        if ($this->options['result_option']) {
            // do something with $result
        }

        $searchContainer->result->setData($result);
        $searchContainer->result->setHitCount(count($result));

        return $searchContainer;
    }
}
```
