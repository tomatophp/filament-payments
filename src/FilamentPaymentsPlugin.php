<?php

namespace TomatoPHP\FilamentPayments;

use Filament\Contracts\Plugin;
use Filament\Panel;


class FilamentPaymentsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-payments';
    }

    public function register(Panel $panel): void
    {
        //
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
