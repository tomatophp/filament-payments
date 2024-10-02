![Screenshot](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/3x1io-tomato-payments.jpg)

# Filament Payment Manager

[![Latest Stable Version](https://poser.pugx.org/tomatophp/filament-payments/version.svg)](https://packagist.org/packages/tomatophp/filament-payments)
[![License](https://poser.pugx.org/tomatophp/filament-payments/license.svg)](https://packagist.org/packages/tomatophp/filament-payments)
[![Downloads](https://poser.pugx.org/tomatophp/filament-payments/d/total.svg)](https://packagist.org/packages/tomatophp/filament-payments)

Manage your payments inside FilamentPHP app with multi payment gateway integration

## Screenshots

![Payment Page](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/payment-page.png)
![Payments](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/payments.png)
![View Payment](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/view.png)
![Payment Gates](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/payment-gates.png)
![Edit Gate](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/edit-gate.png)
![Gate Option](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/gate-option.png)
![Payment Action](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/payment-action.png)
![Payment Action Confirm](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/action-confirm.png)

## Features

- [x] Payments List
- [x] Payment View
- [x] Payment Filter And Groups by Status
- [x] Payment Gates
- [x] Payment Gate Options
- [x] Payment Action
- [x] Payment Facade Method
- [x] Payment Page
- [x] Payment Drivers
- [x] Strip3 Integration
- [x] Plisio Integration
- [x] Paypal Integration
- [x] Paymob Integration
- [x] Tap Integration
- [x] Myfatoorah Integration
- [ ] Paddle Integration
- [ ] Lemon Squeezy Integration
- [ ] Binance Integration
- [ ] Creptomus Integration
- [ ] PayTabs Integration
- [ ] Moyaser Integration
- [ ] Payfort Integration
- [ ] Fawery Integration

## Installation

```bash
composer require tomatophp/filament-payments
```

we need the Media Library plugin to be installed and migrated you can use this command to publish the migration

```bash
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
```

after install your package please run this command

```bash
php artisan filament-payments:install
```

finally register the plugin on `/app/Providers/Filament/AdminPanelProvider.php`

```php
->plugin(\TomatoPHP\FilamentPayments\FilamentPaymentsPlugin::make())
```

## Using

you can use payment with the very easy way just use Facade `FilamentPayments` like this

```php
use TomatoPHP\FilamentPayments\Facades\FilamentPayments;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentBillingInfo;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentCustomer;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentRequest;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentShippingInfo;
use TomatoPHP\FilamentSubscriptions\Facades\FilamentSubscriptions;

return redirect()->to(
        FilamentPayments::pay(
            data: PaymentRequest::make(Plan::class)
                ->model_id($data['new']->id)
                ->currency('USD')
                ->amount($data['new']->price)
                ->details('Subscription Payment')
                ->success_url(url('/success'))
                ->cancel_url(url('/cancel'))
                ->customer(
                    PaymentCustomer::make('John Doe')
                        ->email('john@gmail.com')
                        ->mobile('+201207860084')
                )
                ->billing_info(
                    PaymentBillingInfo::make('123 Main St')
                        ->area('Downtown')
                        ->city('Cairo')
                        ->state('Cairo')
                        ->postcode('12345')
                        ->country('EG')
                )
                ->shipping_info(
                    PaymentShippingInfo::make('123 Main St')
                        ->area('Downtown')
                        ->city('Cairo')
                        ->state('Cairo')
                        ->postcode('12345')
                        ->country('EG'
                        )
                )),
    );
```

if you want to return it as json you can just make `json: true`, this method return a URL for you with the payment, you can share this link with anyone to make the payment done.

## Use Payment Action

you can use a Table Action to make it easy to link Payment with your table like this

```php
use TomatoPHP\FilamentPayments\Filament\Actions\PaymentAction;

public function table(Table $table): $table
{
    return $table
        ->actions([
             PaymentAction::make('payment')
                ->request(function ($record){
                    return PaymentRequest::make(Order::class)
                        ->model_id($record->id)
                        ->currency('USD')
                        ->amount($record->total)
                        ->details($record->ordersItems()->pluck('product_id')->implode(', '))
                        ->success_url(url('/success'))
                        ->cancel_url(url('/cancel'))
                        ->customer(
                            PaymentCustomer::make($record->name)
                                ->email($record->account->email)
                                ->mobile($record->phone)
                        )
                        ->billing_info(
                            PaymentBillingInfo::make($record->address)
                                ->area($record->area->name)
                                ->city($record->city->name)
                                ->state($record->city->name)
                                ->postcode('12345')
                                ->country($record->country->iso3)
                        )
                        ->shipping_info(
                            PaymentShippingInfo::make($record->address)
                                ->area($record->area->name)
                                ->city($record->city->name)
                                ->state($record->city->name)
                                ->postcode('12345')
                                ->country($record->country->iso3)
                        );
                })
                ->pay(),
        ]);
}
```

## Integrate With Filament Subscription

if you like to use this package with [Filament Subscription](https://www.github.com/tomatophp/filament-subscriptions) you can use this code

```php
use TomatoPHP\FilamentPayments\Facades\FilamentPayments;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentBillingInfo;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentCustomer;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentRequest;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentShippingInfo;
use TomatoPHP\FilamentSubscriptions\Facades\FilamentSubscriptions;

public function boot(): void
    {
        FilamentSubscriptions::afterSubscription(function ($data) {
            //Payment Here
            return redirect()->to(FilamentPayments::pay(
                data: PaymentRequest::make(Plan::class)
                    ->model_id($data['new']->id)
                    ->currency('USD')
                    ->amount($data['new']->price)
                    ->details('Subscription Payment')
                    ->success_url(url('/success'))
                    ->cancel_url(url('/cancel'))
                    ->customer(
                        PaymentCustomer::make('John Doe')
                            ->email('john@gmail.com')
                            ->mobile('+201207860084')
                    )
                    ->billing_info(
                        PaymentBillingInfo::make('123 Main St')
                            ->area('Downtown')
                            ->city('Cairo')
                            ->state('Cairo')
                            ->postcode('12345')
                            ->country('EG')
                    )
                    ->shipping_info(
                        PaymentShippingInfo::make('123 Main St')
                            ->area('Downtown')
                            ->city('Cairo')
                            ->state('Cairo')
                            ->postcode('12345')
                            ->country('EG')
                    )
            ));
        });
    }
```

it will redirect you to payment after the hook is called.

## Add Custom Payment Driver 

you need to create a new class extends `TomatoPHP\FilamentPayments\Services\Drivers\Driver` like this

```php
<?php

namespace TomatoPHP\FilamentPayments\Services\Drivers;

use Illuminate\Http\Request;
use TomatoPHP\FilamentPayments\Models\Payment;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentCurrency;
use TomatoPHP\FilamentPayments\Services\Contracts\PaymentGateway;

class Stripe extends Driver
{
    public static function process(Payment $payment): false|string
    {
        $stripeData = $payment->gateway->gateway_parameters;
        \Stripe\Stripe::setApiKey($stripeData['secret_key']);

        try {
            $session = \Stripe\PaymentIntent::create([
                'amount' => round($payment->amount + $payment->charge, 2) * 100,
                'currency' => "$payment->method_currency",
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);
        } catch (\Exception $e) {
            $send['error'] = true;
            $send['message'] = $e->getMessage();
            return json_encode($send);
        }

        $send['view'] = 'filament-payments::payment.StripeEmbedded';
        $send['session'] = $session->id;
        $send['client_secret'] = $session->client_secret;
        $send['publishable_key'] = $stripeData['publishable_key'];
        $send['success_url'] = route('payments.callback', 'Stripe');

        $payment->method_code = json_decode(json_encode($session))->id;
        $payment->save();
        return json_encode($send);
    }

    public static function verify(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
    {
        $StripeAcc = \TomatoPHP\FilamentPayments\Models\PaymentGateway::where('alias', 'Stripe')->orderBy('id', 'desc')->firstOrFail();
        $gateway_parameter = $StripeAcc->gateway_parameters;

        \Stripe\Stripe::setApiKey($gateway_parameter['secret_key']);
        $stripeSession = $request->get('payment_intent');
        $session = \Stripe\PaymentIntent::retrieve($stripeSession);

        $payment = Payment::where('method_code',  $session->id)->where('status', 0)->firstOrFail();

        if ($session->status === 'succeeded') {

            self::paymentDataUpdate($payment);

            return redirect($payment->success_url);
        }

        self::paymentDataUpdate($payment, true);
        return redirect($payment->failed_url);
    }

    public function integration(): array
    {
        return PaymentGateway::make('StripeEmbedded')
            ->alias('Stripe')
            ->status(true)
            ->crypto(false)
            ->gateway_parameters([
                "secret_key" => "",
                "publishable_key" => ""
            ])
            ->supported_currencies([
                PaymentCurrency::make('USD')
                    ->symbol('$')
                    ->rate(1)
                    ->minimum_amount(1)
                    ->maximum_amount(1000)
                    ->fixed_charge(0.2)
                    ->percent_charge(2)
                    ->toArray()
            ])
            ->toArray();
    }
}
```

then you need to register the new driver on your `filament-payments.php` config file like this

```php
return [
    "drivers" => [
        \TomatoPHP\FilamentPayments\Services\Drivers\Fawery::class,
        \TomatoPHP\FilamentPayments\Services\Drivers\Moyaser::class,
        \TomatoPHP\FilamentPayments\Services\Drivers\Payfort::class,
        \TomatoPHP\FilamentPayments\Services\Drivers\Paymob::class,
        \TomatoPHP\FilamentPayments\Services\Drivers\Paypal::class,
        \TomatoPHP\FilamentPayments\Services\Drivers\Paytabs::class,
        \TomatoPHP\FilamentPayments\Services\Drivers\Plisio::class,
        \TomatoPHP\FilamentPayments\Services\Drivers\StripeV3::class,
        \TomatoPHP\FilamentPayments\Services\Drivers\Tap::class,
        \App\Drivers\YourDriver::class
    ],
    "path" => "TomatoPHP\\FilamentPayments\\Services\\Drivers"
];
```


## Publish Assets

you can publish config file by use this command

```bash
php artisan vendor:publish --tag="filament-payments-config"
```

you can publish views file by use this command

```bash
php artisan vendor:publish --tag="filament-payments-views"
```

you can publish languages file by use this command

```bash
php artisan vendor:publish --tag="filament-payments-lang"
```

you can publish migrations file by use this command

```bash
php artisan vendor:publish --tag="filament-payments-migrations"
```


## Other Filament Packages

Checkout our [Awesome TomatoPHP](https://github.com/tomatophp/awesome)
