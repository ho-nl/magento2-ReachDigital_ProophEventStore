# Magento 2 Vault Api Adyen Payment Integration

 Add payment methods for customer on the shop
 Automatically assign the new payment method to the current orders and 'retry the payment'

Current integration uses Magento's old Billing Agreement API. This Api is very limited and requires PSP-specific code to
get it working. It is currently integrated in Magento by PayPal and Adyen. The new Vault Api allows us to store payment
information in a more abstract way. It is currently supported by PayPal, Braintree, Stripe and more. It isn't yet
supported by Adyen.

While it is possible to streamline the Pre-order flow with the current Billing Agreement setup, it isn't a solution
for the Subscription. Since that needs to be build it is better to use actual proper implementations.

## Flow:
1. CreateInvoiceInOdoo calls the Magento REST API to trigger the creation of the invoice in Magento.
2. When the invoice is created in Magento, the Payment is triggered with the new Vault information
    - [Implement Vault]
    
3a. If the payment is successful:
4. The API gives back a positive result, Odoo creates the invoice.
5. Shipment is created an posted back to Odoo.

3b. If the payment has failed:
4. The API throws an error and the Odoo email doesn't get created.
5. The Vault item is marked as invalid.
5. A automatic email for the failed order will be send to the customer to request new Vault information.
    - Implement payment reminder email
6. The customer is asked to pay the outstanding order.
    - [Create new Payment page for existing order: this should be on the VanMoof website and should include]
        - [Order information (billing+shipping+products), same as MyAccount/Orders currently]
        - [Order items + image of purchased product]
        - [Order Payment information edit functionality]
        - [Adyen SEPA UI]
        - [Adyen CC UI]
    - [Implement order payment detail updater]
    - [Order is automatically invoiced with the new payment details]
    - [Vault information is stored for future payments]
    
    We explicitly chose to do it based on an order and not on a customer to provide payment information, as we deep it
    more user friendly to ask for the payment for the outstanding amount, instead of asking for payment details even
    though the pre-order only requires a single payment. The result will be the same, but the user experience will be
    different. e.g. When adding payment information for SEPA using iDEAL it requires us to make a €0,01 transaction
    which is odd if you only need to pay the remaining pre-order fee.
    
    Also if we save information on the customer, we need a way to store which invoices need to be recreated after a
    certain time.
7. Odoo should automatically retry to create the invoice on a later moment (every day for example). Magento will then
    give back an error 'invoice already created' and Odoo will create its own invoice.
    - [Odoo check invoice checker implementation]

The result is that subsequent invoice triggers (from Odoo or form a future subscription system) will now run properly.


## Migration from Billing agreements to the Vault Api
We keep the existing Billing Agreements in the system, this means that the current orders will handle the payment in
the old way.
    - [Implement payment failure notifications for failed Billing Agreement orders and then switch those order to the
    Vault Api]

This way we keep the current functionality working properly and we can add the Vault functionality the moment it is
fully ready.

## Notes:
- Currently in Magento 2.2.2 there already is a Vault integration: One Click Buy / Instant Purchase. This gives Adyen
incentive to actually implement the Vault feature without us having to do all the work.
- In a certain sense a pre-order is a one-time subscription. A subscription starts when the product is 
actually shipped and runs for the amount of terms configured for the subscription. This means that a subscription and a
pre-order is a form of 'automated payment'. Both the Subscription service as well as the Pre order system will be using
the Vault Api.
- For the Magento and Odoo system to work properly, we require (at least in Magento) an Order for each
payment to be made.
- Finance has requested if it is possible for each order to be created in advance. This will allow for better
financial forecasting. We can't actually do that because Order in Magento are create once, change never. This isn't the
case with pricing and payment changes on subscriptions.


# Subscription Api

The specifications below do not focus on design/frontend, that will be handled in a separate document at a later
stage. The goal of the Subscription Api is to achieve a very scalable solution that can handle the Subscription
requirements for VanMoof for the coming years.

## Prebuild solution vs. Custom solution

If possible it is almost always advised to use a prebuild solution (a Magento module) to solve the issue. While
creating all the requirements for the subscription solution we came to the conclusion that we need to build it custom.

The SubscriptionPlan implementation of the offered third party solution is very naive. It doesn't offer a way to
keep a customer on an old version of the subscription while also selling new versions of the subscriptions online.

The custom solution with be build via the Domain Driven Development model using CQRS and Event Sourcing to give us the
possibility to track the state of an entity over time (history). This allows us to keep certain Subcriptions on an old
version of a plan. This old version of the subscription can then be upgraded to the latest version (automatically or
manually by a customer service representative).

Reporting is a very important part of the functionality
- Reporting is a very important part of the functionality ___TODO___

## Models

For each product it will be possible to enter multiple SubscriptionPlan's these plans have the following configuration
fields:
    - status [WEBSITE]
    - enabled
    - enabled_on_frontend
    - label [STORE VIEW]
    - termCount: A fixed number (1-12) or empty for infinite. [GLOBAL]
    - interval: 1 [GLOBAL]
    - intervalUnit: week|month|year [GLOBAL]
    - intervalFixedAt: 1 (for first day of unit) [GLOBAL]
    - autoStop: true|false [GLOBAL]
    - initialPrice: Fee the customer pays in advance [WEBSITE]
    - installmentPrice: Fee the customer pays on each installment. [WEBSITE]

- [Create UI to add multiple subscriptions for each product]
- [Implement buyrequest configuration options to add a product to the cart]
- [Render the subscription information below the product in the cart: '12 terms of €49,00']


When a subscription is placed we define the following fields:
    - SubscriptionPlanId: which actual subscription plan is used.
    - SubscriptionPlanVersion: Which version of the subscription is used.
    - CustomerId
    - BillingAddressId
    - ShippingAddressId
    - VaultTokenId
    

## Commands

### Product Plan commands

- `ProductPlanSetSubscriptionType($product, $type)` (Allow both direct purchase and subscription or only subscription)
    - This will probably be implemented as a custom product attribute. 
- `ProductPlanCreateNew($planId, $product)`
- `ProductPlanTermCount($planId, $termCount)`
- `ProductPlanAutoStop($planId, true)`
    - When the termCount is over the Subscription will automatically halt or continue running.
- `ProductPlanInterval($planId, $interval, $intervalUnit, $intervalFixedAt)`
    - By defining the intervalFixedAt we define that we will only bill on the n-th of the month.
- `ProductPlanInitialPrice($planId, $websiteId, $initialPrice)`
- `ProductPlanInstallmentPrice($planId, $websiteId, $installmentPrice)`
    - ~~Automatically update the planVersion of all the subscriptions~~ The subscriptions will get flagged that it needs
    to be updated.
- `ProductPlanEnable($planId, $storeId, $enabled)`
    - Enable the subscription for customer service employees.
- `ProductPlanEnableOnFrontend($planId, $storeId, $enabled)`
    - Enable the subscription on the frontend of the shop
- `ProductPlanLabel($planId, $storeId, $label)`
    - Label to be shown on the product page / cart page and on the subsequent subscription orders.

### Subscription commands
- `SubscriptionCreateNew($subscriptionId, $orderId, $planId, $planVersion, $customerId)`
    - After the initial order is placed and payment has successfully been created the subscription will be created in
      the system.
- `SubscriptionAssignBillingAddress($subscriptionId, $billingAddressId)`
    - We need to force the customer to save their address from the order in the customers account. Normally this is
     optional.
    - Normally it is possible to delete the address from the customer account, we need to disable that functionality
    if the address is used in a subscription (we can just throw an error when this happens).
- `SubscriptionAssignShippingAddress($subscriptionId, $billingAddressId)`
    - When actual products would be shipped to the customer, we assign the shipping address as well.
- `SubscriptionAssignPaymentToken($subscriptionId, $vaultTokenId)`
    - For this to work the Vault needs to be implemented of course.
- `SubscriptionStart($subscriptionId)`
    - The Subscription will be enabled if the original order is shipped.
- `SubscriptionStop($subscriptionId, $reason)`
    - Manually by the customer service employee
- `SubscriptionCustomerAgreesToNewVersion($subscriptionId, $planVersion)`
- `SubscriptionBillNextTerm($subscriptionId)`
    - Will check if the subscription is active in `projection: subscription_listing`.
    - Will automatically create the order `event: SubscriptionOrderCreated($subscriptionId, $orderSequenceNr, $orderId)`
    - Will automatically create the invoice for the order
        `event: SubscriptionOrderInvoiced($subscriptionId, $orderId, $invoiceId)`
    - `event: SubscriptionDiscountWasApplied($subsscriptionId, $orderId, $amount)`
- `SubscriptionDiscountNextTerm($subscriptionId, $amount)` will automatically make sure the next billing term will be
cheaper than the current term.
    - `event: SubscriptionDiscountWasGiven($subsscriptionId, $amount)`

## Projections
- `subscription_plan_product_link`: Reference form the product to the `SubscriptionPlan`
- `subscription_order_history`: A list of already created orders for the subscription
- `subscription_order_schedule`: A list of orders that will be created sometime in the future (projecting one year in the
future).
- `subscription_listing`: Overview of all the subscriptions.
    - start_date
    - end_date
    - amount
    - next_term_sequence_nr
    - next_term_billing_from
    - next_term_billing_to
    - next_term_billing_days
    - next_term_billing_amount
    - status: started, stopped
    - requires_update: Should the subscription be updated?

Price calculation happens 

## Cron
- From the `subscription_order_schedule` projection we will create the `SubscriptionBillNextTerm($subscription)`
commands for the orders that need to be created.

Q: Should we invoice on the first day of the month or when the order is placed?
A: Preferentially we should invoice on the first day of the month. Two step system.
