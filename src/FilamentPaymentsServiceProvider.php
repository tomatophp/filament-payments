<?php

namespace TomatoPHP\FilamentPayments;

use Illuminate\Support\ServiceProvider;


class FilamentPaymentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //Register generate command
        $this->commands([
           \TomatoPHP\FilamentPayments\Console\FilamentPaymentsInstall::class,
        ]);

        //Register Config file
        $this->mergeConfigFrom(__DIR__.'/../config/filament-payments.php', 'filament-payments');

        //Publish Config
        $this->publishes([
           __DIR__.'/../config/filament-payments.php' => config_path('filament-payments.php'),
        ], 'filament-payments-config');

        //Register Migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        //Publish Migrations
        $this->publishes([
           __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'filament-payments-migrations');
        //Register views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filament-payments');

        //Publish Views
        $this->publishes([
           __DIR__.'/../resources/views' => resource_path('views/vendor/filament-payments'),
        ], 'filament-payments-views');

        //Register Langs
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'filament-payments');

        //Publish Lang
        $this->publishes([
           __DIR__.'/../resources/lang' => base_path('lang/vendor/filament-payments'),
        ], 'filament-payments-lang');

        //Register Routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

    }

    public function boot(): void
    {
        $this->app->bind('filament-payments', function () {
            return new \TomatoPHP\FilamentPayments\Services\FilamentPaymentsServices();
        });
    }
}
