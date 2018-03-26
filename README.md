# Magento 2 Prooph EventStore

Integration with Prooph software and Magento 2's DI.

## Usage

To use the Prooph components in your application, use:
https://github.com/ho-nl/magento2-ProophEventStore/blob/master/src/ProophEventStoreContext.php

```
$this->proophEventStoreContext->commandBus()->dispatch($command);
$this->proophEventStoreContext->eventBus()->dispatch($event);
$this->proophEventStoreContext->queryBus()->dispatch($query)->then(function($result){
  //do something with $result
});
```

### Adding commands

```xml
<type name="ReachDigital\ProophEventStore\Infrastructure\CommandRouter">
  <arguments>
    <argument name="messageMap" xsi:type="array">
      <item name="ReachDigital\MyModule\Model\ProductPlan\Command\MyCommand"
                 xsi:type="object">ReachDigital\MyModule\Model\ProductPlan\Handler\MyCommandHandler</item>
    </argument>
  </arguments>
</type>

```

### Adding queries

```xml
<type name="ReachDigital\ProophEventStore\Infrastructure\QueryRouter">
    <arguments>
        <argument name="messageMap" xsi:type="array">
            <item name="ReachDigital\Subscription\Model\Subscription\Query\GetOrderSchedule"
                  xsi:type="object">ReachDigital\Subscription\Model\Subscription\Handler\GetOrderScheduleHandler</item>
            <item name="ReachDigital\Subscription\Model\Subscription\Query\GetOrderHistory"
                  xsi:type="object">ReachDigital\Subscription\Model\Subscription\Handler\GetOrderHistoryHandler</item>
        </argument>
    </arguments>
</type>
```

### Adding AggregateRoot Collections


```xml
<preference for="ReachDigital\Subscription\Model\Subscription\SubscriptionCollection"
            type="ReachDigital\Subscription\Infrastructure\Repository\EventStoreSubscriptionCollection"/>
<type name="ReachDigital\Subscription\Infrastructure\Repository\EventStoreSubscriptionCollection">
    <arguments>
        <argument name="aggregateRoot" xsi:type="string">ReachDigital\Subscription\Model\Subscription\Subscription</argument>
        <argument name="streamName" xsi:type="string">subscription</argument>
    </arguments>
</type>
```
    
