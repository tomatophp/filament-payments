<?php

namespace TomatoPHP\FilamentPayments;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use TomatoPHP\FilamentPayments\Console\FilamentPaymentsInstall;
use TomatoPHP\FilamentPayments\Livewire\PaymentProcess;
use TomatoPHP\FilamentPayments\Services\FilamentPaymentsServices;

class FilamentPaymentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register generate command
        $this->commands([
            FilamentPaymentsInstall::class,
        ]);

        // Register Config file
        $this->mergeConfigFrom(__DIR__.'/../config/filament-payments.php', 'filament-payments');

        // Publish Config
        $this->publishes([
            __DIR__.'/../config/filament-payments.php' => config_path('filament-payments.php'),
        ], 'filament-payments-config');

        // Register Migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Publish Migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'filament-payments-migrations');

        // Register views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filament-payments');

        // Publish Views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/filament-payments'),
        ], 'filament-payments-views');

        // Register Langs
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'filament-payments');

        // Publish Lang
        $this->publishes([
            __DIR__.'/../resources/lang' => base_path('lang/vendor/filament-payments'),
        ], 'filament-payments-lang');

        $this->app->bind('filament-payments', function () {
            return new FilamentPaymentsServices;
        });
    }

    public function boot(): void
    {
        // Routes read `filament-payments.guard`, so they are loaded once every provider has registered its config.
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        Livewire::component('payment-process', PaymentProcess::class);
    }
}
