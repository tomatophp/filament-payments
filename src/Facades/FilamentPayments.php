<?php

namespace TomatoPHP\FilamentPayments\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void loadDrivers()
 */
class FilamentPayments extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'filament-payments';
    }
}
