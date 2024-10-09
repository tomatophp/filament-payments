<?php

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
    ],
    "path" => "TomatoPHP\\FilamentPayments\\Services\\Drivers"
];
