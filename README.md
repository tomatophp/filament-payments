![Screenshot](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/3x1io-tomato-payments.jpg)

# Filament Payment Manager

[![Latest Stable Version](https://poser.pugx.org/tomatophp/filament-payments/version.svg)](https://packagist.org/packages/tomatophp/filament-payments)
[![License](https://poser.pugx.org/tomatophp/filament-payments/license.svg)](https://packagist.org/packages/tomatophp/filament-payments)
[![Downloads](https://poser.pugx.org/tomatophp/filament-payments/d/total.svg)](https://packagist.org/packages/tomatophp/filament-payments)

Manage your payments inside FilamentPHP app with multi payment gateway integration

## Screenshots

![Payments](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/payments-light.png)
![Payments Dark](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/payments-dark.png)
![View Payment](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/view-payment-light.png)
![View Payment Dark](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/view-payment-dark.png)
![Payment Gateways](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/gateways-light.png)
![Payment Gateways Dark](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/gateways-dark.png)
![Edit Gateway](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/edit-gateway-light.png)
![Edit Gateway Dark](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/edit-gateway-dark.png)
![Payment Page](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/payment-page-light.png)
![Payment Page Dark](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/payment-page-dark.png)

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
- [x] StripeV3 Integration
- [x] Plisio Integration
- [x] Paypal Integration
- [x] Paymob Integration
- [x] Tap Integration
- [x] Myfatoorah Integration
- [x] Creptomus Integration
- [ ] Paddle Integration
- [ ] Lemon Squeezy Integration
- [ ] Binance Integration
- [ ] PayTabs Integration
- [ ] Moyaser Integration
- [ ] Payfort Integration
- [ ] Fawery Integration

## Compatibility

| Package version | Filament | Laravel | PHP  |
|-----------------|----------|---------|------|
| 5.x             | 5.x      | 12 / 13 | 8.2+ |
| 1.x (`v3` branch) | 3.x    | 10 / 11 | 8.1+ |

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

## Payment Page Guard

The payment page (`/pay/{trx}`) requires an authenticated user. Since v5 it uses `config('filament-payments.guard')`, which defaults to your app's default guard (before v5 it was hard-coded to `auth:accounts`). To use another guard, or to get the old behaviour back, publish the config and set:

```php
// config/filament-payments.php
'guard' => 'accounts',
```

## Gateway Secrets

Gateway parameters are stored encrypted with your `APP_KEY`, and secret parameters are never sent to the browser: in the gateway edit modal they are empty password inputs, and leaving one blank keeps the current value. Each driver declares its secret keys; a custom driver opts in like this:

```php
public static function secretKeys(): array
{
    return ['secret_key'];
}
```

If you rotate `APP_KEY`, re-enter your gateway keys.

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
