<?php

namespace TomatoPHP\FilamentPayments;

use Filament\Contracts\Plugin;
use Filament\Panel;
use TomatoPHP\FilamentPayments\Filament\Resources\PaymentGatewayResource;
use TomatoPHP\FilamentPayments\Filament\Resources\PaymentResource;

class FilamentPaymentsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-payments';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            PaymentGatewayResource::class,
            PaymentResource::class
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return new static();
    }
}
