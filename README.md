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
- When you are creating new entities
- When you are creating new Commands
- When you want to have a high development velocity

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

#### Field Types, Commands and events with FPP

Generate your classes with [fpp](https://github.com/prolic/fpp) (there is a PHPStorm file watcher for fast development).

Logical Magento Module: https://github.com/ho-nl/magento2-ReachDigital-TransferOrdersES/tree/master/TransferOrdersESApi
Domain Model: https://github.com/ho-nl/magento2-ReachDigital-TransferOrdersES/blob/master/TransferOrdersESApi/etc/domain.fpp
Generated Code: https://github.com/ho-nl/magento2-ReachDigital-TransferOrdersES/tree/master/TransferOrdersESApi/Model

Domain Model: https://github.com/ho-nl/magento2-ReachDigital_Subscription/blob/master/src/Model/Domain.fpp

To create your own Domain model, understand this http://docs.getprooph.org/tutorial/why_event_sourcing.html

#### Your Model (AggregateRoot)

Create your Domain Model (AggregateRoot). Because this model actually contains business logic, this class can't be
auto generated. This is a bit of chore, but writing all your commands, events, fields down in the class validates your
domain model.

This class is a leaky abstraction: This class actually defines the business logic required and belongs in the domain
model, but it extends AggregateRoot which is an implementation specific thing.. Therefor it is a bit of odd class here.

Examples:
https://github.com/ho-nl/magento2-ReachDigital-TransferOrdersES/blob/master/TransferOrdersESApi/Model/Transfer/Transfer.php
https://github.com/ho-nl/magento2-ReachDigital_Subscription/blob/master/src/Model/Subscription/Subscription.php
https://github.com/ho-nl/magento2-ReachDigital_Subscription/blob/master/src/Model/ProductPlan/ProductPlan.php

#### GetTransferInterface + SaveTransferInterface

Since we dont want any save/load logic in this module, we're defining the interfaces here.

https://github.com/ho-nl/magento2-ReachDigital-TransferOrdersES/blob/ab296875bce658196775b911edb9892e492a6012/TransferOrdersESApi/Model/Transfer/GetTransferInterface.php
https://github.com/ho-nl/magento2-ReachDigital-TransferOrdersES/blob/ab296875bce658196775b911edb9892e492a6012/TransferOrdersESApi/Model/Transfer/SaveTransferInterface.php

## Event Store setup (chore)

We're now implementing the Command side of CQRS (Command Query Responsibility Seggegation). This means we need to
implement the GetTransferInterface, SaveTransferInterface.

Create a second module (composer.json PSR4, registration.php, module.xml) and enable the module via php bin/magento
module enable, make sure it works.

Create a event store table by creating a SchemaPatch:
https://github.com/ho-nl/magento2-ReachDigital-TransferOrdersES/blob/ab296875bce658196775b911edb9892e492a6012/TransferOrdersES/Setup/Patch/Schema/CreateEventStore.php

To access the information of the event store, use the [AggregateRepository](http://docs.getprooph.org/event-sourcing/repositories.html#4-2-6) to fetch the information.

https://github.com/ho-nl/magento2-ReachDigital-TransferOrdersES/blob/ab296875bce658196775b911edb9892e492a6012/TransferOrdersES/Model/Transfer/GetTransfer.php
https://github.com/ho-nl/magento2-ReachDigital-TransferOrdersES/blob/ab296875bce658196775b911edb9892e492a6012/TransferOrdersES/Model/Transfer/SaveTransfer.php
https://github.com/ho-nl/magento2-ReachDigital-TransferOrdersES/blob/ab296875bce658196775b911edb9892e492a6012/TransferOrdersES/etc/di.xml

## Command Handlers

Now onto the meat of the application, making everything work. First we create handlers (with tests for the application).

https://github.com/ho-nl/magento2-ReachDigital-TransferOrdersES/tree/7512a2ad62f297cdb31f96e84161dab884867e29/TransferOrdersES/Model/Transfer/Handler
https://github.com/ho-nl/magento2-ReachDigital-TransferOrdersES/blob/7512a2ad62f297cdb31f96e84161dab884867e29/TransferOrdersES/etc/di.xml#L10-L27
https://github.com/ho-nl/magento2-ReachDigital-TransferOrdersES/tree/7512a2ad62f297cdb31f96e84161dab884867e29/TransferOrdersES/Test/Integration/Model/Handler

In the examples I first focus on the internals of the application, and don't bother with stuff that interacts with the
rest of Magento. This way I can focus on this part of the application, which keeps everything simple.

### Interaction with the rest of the system
A feature usually doesn't exist in a vacuum, so we need to integrate it with the rest of Magento.

## Admin UI

- Controllers
- UI

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
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <type name="ReachDigital\ProophEventStore\Infrastructure\CommandRouter">
      <arguments>
        <argument name="messageMap" xsi:type="array">
          <item name="ReachDigital\MyModule\Model\ProductPlan\Command\MyCommand"
                     xsi:type="object">ReachDigital\MyModule\Model\ProductPlan\Handler\MyCommandHandler</item>
        </argument>
      </arguments>
    </type>
</config>
```

### Adding queries

```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
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
</config>
```

### Adding AggregateRoot Collections


```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <preference for="ReachDigital\Subscription\Model\Subscription\SubscriptionCollection"
                type="ReachDigital\Subscription\Infrastructure\Repository\EventStoreSubscriptionCollection"/>
    <type name="ReachDigital\Subscription\Infrastructure\Repository\EventStoreSubscriptionCollection">
        <arguments>
            <argument name="aggregateRoot" xsi:type="string">ReachDigital\Subscription\Model\Subscription\Subscription</argument>
            <argument name="streamName" xsi:type="string">subscription</argument>
        </arguments>
    </type>
</config>
```
  
## Setting up crons

```
* * * * * flock ~/.transferOrderGridLock php bin/magento event-store:projection:run transferOrderGrid
* * * * * flock ~/.someOtherProjection php bin/magento event-store:projection:run someOtherProjection
```

Then after deployment do:
```
php bin/magento event-store:projection:reset transferOrderGrid
php bin/magento event-store:projection:reset someOtherProjection
```

This will automatically completely regenerate the projection. This might not be completely ideal as this is a full
reindex, but since we dont know if the projection class or db schema has changed we can't know for sure if it is 
required.

Also this requires modifying the cron when a projection changes, so this requires some additional todo's
