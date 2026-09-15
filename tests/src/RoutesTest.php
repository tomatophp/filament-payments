<?php

use Illuminate\Support\Facades\Route;

it('protects the payment page with the configured guard', function () {
    expect(config('filament-payments.guard'))->toBe('testing')
        ->and(Route::getRoutes()->getByName('payment.index')->gatherMiddleware())
        ->toContain('auth:testing')
        ->not->toContain('auth:accounts');
});

it('keeps the gateway callback public', function () {
    expect(Route::getRoutes()->getByName('payments.callback')->gatherMiddleware())
        ->not->toContain('auth:testing');
});
