![Screenshot](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/3x1io-tomato-payments.jpg)

# Filament Payment Manager

[![Latest Stable Version](https://poser.pugx.org/tomatophp/filament-payments/version.svg)](https://packagist.org/packages/tomatophp/filament-payments)
[![License](https://poser.pugx.org/tomatophp/filament-payments/license.svg)](https://packagist.org/packages/tomatophp/filament-payments)
[![Downloads](https://poser.pugx.org/tomatophp/filament-payments/d/total.svg)](https://packagist.org/packages/tomatophp/filament-payments)

Manage your payments inside FilamentPHP app with multi payment gateway integration

## Screenshots

![Payment List](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/list.png)
![Payment View](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/view-payment.png)
![Payment Gateways](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/gateways.png)
![Edit Gateway](https://raw.githubusercontent.com/tomatophp/filament-payments/master/arts/edit-gateway.png)

## Installation

```bash
composer require tomatophp/filament-payments
```
after install your package please run this command

```bash
php artisan filament-payments:install
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
