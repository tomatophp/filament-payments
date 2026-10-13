<?php

return [
    "drivers" => [
        \TomatoPHP\FilamentPayments\Services\Drivers\Cryptomus::class,
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
    "path" => "TomatoPHP\\FilamentPayments\\Services\\Drivers",

    // Only the payment owner (the payment's model, a team/tenant the user can
    // access, or the customer with the same email) can open or cancel a payment.
    "enforce_owner" => true,

    // Minutes the signed payment links stay valid (null = never expire).
    "links" => [
        "pay_expires" => 60 * 24,
        "cancel_expires" => 60 * 24 * 3,
    ],
];
