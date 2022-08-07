# Stripe payment links

This page will guide you through the configuration and usage of Stripe Payment Links for your Easy!Appointments installation.
In order to follow these steps, you will need first to create a Stripe account.
The Stripe account creation and Payment link are not 
part of this guide, please refer to Stripe documentation to know more about it.

## Configuration

Search for the STRIPE PAYMENT INTEGRATION section in `config.php` file and set your stripe API key to constant `STRIPE_API_KEY`

```php

// ------------------------------------------------------------------------
// STRIPE PAYMENT INTEGRATION
// ------------------------------------------------------------------------

const STRIPE_PAYMENT_FEATURE = TRUE; // Enter TRUE to enable Stripe feature
const STRIPE_API_KEY   = '<YOUR-STRIPE-API-KEY-HERE>';

```

## Usage

This guide does not provide information on how to create an account on Stripe or how to create a Stripe Payment link.  
Please refer to Stripe documentation to get more information about it.

### Add a payment link to a service

When editing a service, there is a new field for specifying the payment link. Fill in this field using the payment link created in the Stripe application.

By default, the generated payment link will send the appointment hash in 'client_reference_id'
url parameter and customer email in 'prefilled_email' url parameter to Stripe.
For example, the url 'https://buy.stripe.com/test_eVa3do41l4Ye6KkcMN' will end in 

    https://buy.stripe.com/test_eVa3do41l4Ye6KkcMN?prefilled_email={$customer_email}&client_reference_id={$appointment_hash}

Please refer to [Stripe Payment Links Url parameters](https://stripe.com/docs/payments/payment-links#url-parameters) for more information

### View payment state in calendar view.

It is possible to see the current payment status in the calendar view popup. The icon with a red cross indicates that the payment has not been made, the green check icon indicates that the payment has been made.

When editing the event, you can use the switch to change the payment status of the event. By changing the status and clicking on save, an email will be sent to the user with the URL to process the payment in case the switch is disabled or a payment completed confirmation message if the switch is enabled.

### Email information

#### Services without a payment link

Services without a payment link will not show any payment information.

#### Services with a payment link

Services with a payment link can be in two different states: paid or unpaid.

For unpaid ones, the email will contain a section with the link to process the payment.

For paid services, the email will contain a text indicating the payment is correctly processed.


