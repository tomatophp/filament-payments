<?php

use Illuminate\Support\Facades\Route;
use TomatoPHP\FilamentPayments\Http\Controllers\PaymentController;
use TomatoPHP\FilamentPayments\Livewire\PaymentProcess;
use TomatoPHP\FilamentSubscriptions\Http\Middleware\VerifyBillableIsSubscribed;

Route::domain(config('filament-tenancy.central_domain'))->middleware(['web'])->group(function () {
    $route = Route::name('payment.')->prefix('pay')->middleware(['auth:'.config('filament-payments.guard')]);

    if (class_exists(VerifyBillableIsSubscribed::class)) {
        $route->withoutMiddleware([VerifyBillableIsSubscribed::class]);
    }

    $route->group(function () {
        // Static paths first, otherwise `{trx}` swallows them.
        Route::post('initiate', [PaymentController::class, 'initiate'])->name('initiate');
        Route::get('info', [PaymentController::class, 'info'])->name('info');
        Route::get('{trx}', PaymentProcess::class)->middleware('signed')->name('index');
        // Signed GET is what gateways redirect the customer back to; the pay page itself cancels with the CSRF protected POST.
        Route::get('{trx}/cancel', [PaymentController::class, 'cancel'])->middleware('signed')->name('cancel');
        Route::post('{trx}/cancel', [PaymentController::class, 'cancel'])->name('cancel.submit');
    });

    Route::any('pay/callback/{gateway}', [PaymentController::class, 'verify'])->name('payments.callback');
});
