<?php

namespace TomatoPHP\FilamentPayments;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Nwidart\Modules\Module;
use TomatoPHP\FilamentPayments\Filament\Pages\PaymentGateway;
use TomatoPHP\FilamentPayments\Filament\Resources\PaymentGatewayResource;
use TomatoPHP\FilamentPayments\Filament\Resources\PaymentResource;

class FilamentPaymentsPlugin implements Plugin
{
    private bool $isActive = false;

    public function getId(): string
    {
        return 'filament-payments';
    }

    public function register(Panel $panel): void
    {
        if(class_exists(Module::class) && \Nwidart\Modules\Facades\Module::find('FilamentPayments')?->isEnabled()){
            $this->isActive = true;
        }
        else {
            $this->isActive = true;
        }

        if($this->isActive) {
            $panel
                ->pages([
                    PaymentGateway::class
                ])
                ->resources([
                    PaymentResource::class
                ]);
        }
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
