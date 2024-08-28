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
- [ ] PayTabs Integration
- [ ] Tap Integration
- [ ] Moyaser Integration
- [ ] Payfort Integration
- [ ] Fawery Integration

## Installation

```bash
composer require tomatophp/filament-payments
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

## Support

you can join our discord server to get support [TomatoPHP](https://discord.gg/Xqmt35Uh)

## Docs

you can check docs of this package on [Docs](https://docs.tomatophp.com/plugins/laravel-package-generator)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

Please see [SECURITY](SECURITY.md) for more information about security.

## Credits

- [Fady Mondy](mailto:info@3x1.io)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
