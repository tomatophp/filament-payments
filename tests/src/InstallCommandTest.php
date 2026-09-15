<?php

use TomatoPHP\FilamentPayments\Console\FilamentPaymentsInstall;
use TomatoPHP\FilamentPayments\Models\PaymentGateway;

use function Pest\Laravel\artisan;

it('runs the install command', function () {
    // The real command shells out to `php artisan migrate`; skip that sub-process in tests.
    app()->bind(FilamentPaymentsInstall::class, fn () => new class extends FilamentPaymentsInstall
    {
        public array $ran = [];

        public function artisanCommand(array $command, ?bool $withOutput = false): void
        {
            $this->ran[] = $command;
        }
    });

    artisan('filament-payments:install')
        ->expectsOutputToContain('Filament Payments installed successfully.')
        ->assertSuccessful();

    // Fawery, Moyaser, Payfort and Paytabs are placeholders without an integration yet.
    expect(PaymentGateway::query()->pluck('alias')->sort()->values()->all())
        ->toBe(['Cryptomus', 'Paymob', 'Paypal', 'Plisio', 'StripeV3', 'Tap']);
});
