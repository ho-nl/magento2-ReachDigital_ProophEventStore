# Magento 2 Prooph EventStore

Integration with Prooph software and Magento 2's DI.
Reduce boilerplate in setting up your project.

## Installation

https://github.com/prolic/fpp/blob/master/docs/PhpStorm-Integration.md
https://github.com/ho-nl/docs-internal/issues/22

## Modules build with ES

https://github.com/ho-nl/magento2-ReachDigital_Subscription
https://github.com/ho-nl/magento2-ReachDigital_ProophJira
https://github.com/ho-nl/magento2-ReachDigital-TransferOrdersES

## What is ES?
https://www.youtube.com/watch?v=B6XUEoZlsWk
http://getprooph.org/
http://docs.getprooph.org/tutorial/
https://github.com/prooph/proophessor-do

## When to use ES?
When you are creating new entities.
When you want to have a high development velocity.

## Building your own ES based module

For a full example, take a look at [ReachDigital_TransferOrdersES](https://github.com/ho-nl/magento2-ReachDigital-TransferOrdersES),
which implements all the patterns discussed here. [ReachDigital_Subscription](https://github.com/ho-nl/magento2-ReachDigital_Subscription)
is actually in production so everything is more stable out, but doesn't follow all patterns described here and therefor
is a bit messy.

The Module we are going to build will be split up in multiple logical Magento Modules. We define the following sections:
- Api
- Command+Event Implementation
- Query Implementation
- Frontend UI
- Backend UI

### Api Module

Must contain all:
- Types
- Commands
- Events

Generate your classes with [fpp](https://github.com/prolic/fpp) (there is a PHPStorm file watcher for fast development).

Logical Magento Module: https://github.com/ho-nl/magento2-ReachDigital-TransferOrdersES/tree/master/TransferOrdersESApi
Domain Model: https://github.com/ho-nl/magento2-ReachDigital-TransferOrdersES/blob/master/TransferOrdersESApi/etc/domain.fpp
Generated Code: https://github.com/ho-nl/magento2-ReachDigital-TransferOrdersES/tree/master/TransferOrdersESApi/Model

Domain Model: https://github.com/ho-nl/magento2-ReachDigital_Subscription/blob/master/src/Model/Domain.fpp

To create your own Domain model, understand this http://docs.getprooph.org/tutorial/why_event_sourcing.html

TODO: Create, Read, Update, Delete are forbidden words, what are good Command/Event names?

## Command+Event Implementation




## Usage

To use the Prooph components in your application, use:
https://github.com/ho-nl/magento2-ProophEventStore/blob/master/src/ProophEventStoreContext.php

```php
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
    
