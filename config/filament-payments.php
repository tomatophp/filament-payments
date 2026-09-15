<?php

use TomatoPHP\FilamentPayments\Services\Drivers\Cryptomus;
use TomatoPHP\FilamentPayments\Services\Drivers\Fawery;
use TomatoPHP\FilamentPayments\Services\Drivers\Moyaser;
use TomatoPHP\FilamentPayments\Services\Drivers\Payfort;
use TomatoPHP\FilamentPayments\Services\Drivers\Paymob;
use TomatoPHP\FilamentPayments\Services\Drivers\Paypal;
use TomatoPHP\FilamentPayments\Services\Drivers\Paytabs;
use TomatoPHP\FilamentPayments\Services\Drivers\Plisio;
use TomatoPHP\FilamentPayments\Services\Drivers\StripeV3;
use TomatoPHP\FilamentPayments\Services\Drivers\Tap;

return [
    'drivers' => [
        Cryptomus::class,
        Fawery::class,
        Moyaser::class,
        Payfort::class,
        Paymob::class,
        Paypal::class,
        Paytabs::class,
        Plisio::class,
        StripeV3::class,
        Tap::class,
    ],
    'path' => 'TomatoPHP\\FilamentPayments\\Services\\Drivers',
    'guard' => config('auth.defaults.guard'),
];
