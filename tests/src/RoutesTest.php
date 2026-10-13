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

it('signs the payment page and the gateway cancel return, and cancels from the page with a POST', function () {
    $routes = Route::getRoutes();

    expect($routes->getByName('payment.index')->gatherMiddleware())->toContain('signed')
        ->and($routes->getByName('payment.cancel')->gatherMiddleware())->toContain('signed')
        ->and($routes->getByName('payment.cancel')->methods())->not->toContain('POST')
        ->and($routes->getByName('payment.cancel.submit')->methods())->toContain('POST')
        ->and($routes->getByName('payment.cancel.submit')->methods())->not->toContain('GET');
});
