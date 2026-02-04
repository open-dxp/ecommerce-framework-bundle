# Payment Manager

The Payment Manager is responsible for all aspects concerning payment. The main aspect is the implementation
of different Payment Provider to integrate them into the framework. 

## Basic workflow
   1. [SHOP] Init payment provider (`$payment->initPayment()`).
   2. [SHOP] User click pay button and is redirected to the payment provider page.
   3. [PAYMENT PROVIDER] User fill up payment information and is redirected back to the shop.
   4. [SHOP] Check if the payment is authorised (`$payment->handleResponse()`). At this step the order can be committed.
   5. [SHOP] Clearing payment if its required (`$payment->executeDebit()`)
   
For more information about integrating Payment into checkout processes see 
[Integrating Payment Docs](../13_Checkout_Manager/07_Integrating_Payment.md). 


## Configuration

Configuration of Payment Manager takes place in the `opendxp_ecommerce_config.payment_manager` config section: 

```yaml
opendxp_ecommerce_config:
    payment_manager:
        # service ID of payment manager implementation - following value is default value an can be omitted
        payment_manager_id: OpenDxp\Bundle\EcommerceFrameworkBundle\PaymentManager\PaymentManager

        # configuration of payment providers, key is name of provider
        providers:
            datatrans:
                # service ID of payment provider implementation
                provider_id: OpenDxp\Bundle\EcommerceFrameworkBundle\PaymentManager\Payment\Datatrans

                # active profile - you can define multiple profiles in the section below 
                profile: sandbox

                # available profiles with options - options vary on the provider implementation as the
                profiles:
                    sandbox:
                        merchant_id: 1000011011
                        sign: 30916165706580013
                        use_digital_signature: false
                    live:
                        merchant_id: merchant_id_id
                        sign: sign_id
                        use_digital_signature: false
                        mode: live

            ogone:
                provider_id: OpenDxp\Bundle\EcommerceFrameworkBundle\PaymentManager\Payment\OGone
                profile: sandbox
                profiles:
                    sandbox:
                        secret: D343DDFD3434
                        pspid: MyTestAccount
                        mode: sandbox                        
#                       encryptionType: SHA256 or SHA512 (optional)                                              
                    live:
                        secret: D343DDFD3434
                        pspid: MyLiveAccount
                        mode: live                        
#                       encryptionType: SHA256 or SHA512 (optional)
            mpay24:
                provider_id: OpenDxp\Bundle\EcommerceFrameworkBundle\PaymentManager\Payment\Mpay24Seamless
                profile: testsystem
                profiles:
                  _defaults:
                      #paypal_activate_item_level: true
                      partial: Shared/Includes/Shop/Payment/paymentMethods.html.php
                      payment_methods:
                          cc:
                          paypal:
                          sofort:
                          invoice:
                  testsystem:
                      merchant_id: 95387
                      password: 7&jcQ%v6RB
                      testSystem: true
                      debugMode: true
                  live:
                      merchant_id: todo
                      password: todo
                      testSystem: false
                      debugMode: false                  
            hobex:
                    provider_id: OpenDxp\Bundle\EcommerceFrameworkBundle\PaymentManager\Payment\Hobex                    
                    profile: sandbox
                    profiles:
                        sandbox:
                            entityId: '8a829418530df1d201531299e097175c'
                            authorizationBearer: 'OGE4Mjk0MTg1MzBkZjFkMjAxNTMxMjk5ZTJjMTE3YWF8ZzJnU3BnS2hLUw=='
                            testSystem: true
                            payment_methods:
                                - VISA
                                - MASTER
                                - SOFORTUEBERWEISUNG
                                - SEPA                                                                 
```

The payment provider name will be referenced from the checkout manager configuration and can be used to fetch a specific
provider from the payment manager.

## Payment Providers
Following Payment Providers are available as a dedicated bundle to be integrated into the E-commerce framework: 

- [PayPalSmartButton](https://github.com/open-dxp/payment-provider-paypal-smart-payment-button-bundle)
- [Unzer (former Heidelpay)](https://github.com/open-dxp/payment-provider-unzer-bundle)

### Other Payment Providers (not yet migrated to OpenDXP; contact us for more information)
- [Datatrans](https://github.com/open-dxp/payment-provider-datatrans-bundle)
- [Klarna](https://github.com/open-dxp/payment-provider-klarna-bundle)
- [OGone](https://github.com/open-dxp/payment-provider-ogone-bundle)
- [MPay24](https://github.com/open-dxp/payment-provider-mpay24-seamless-bundle)
- [PayU](https://github.com/open-dxp/payment-provider-payu-bundle)
- [Hobex](https://github.com/open-dxp/payment-provider-hobex-bundle)

## Further Payment Aspects
- [Recurring Payments](10_Recurring_Payments.md)
